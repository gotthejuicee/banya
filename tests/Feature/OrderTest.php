<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Jobs\SendOrderToTelegram;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\SeedsLanding;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    use SeedsLanding;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLandingFixtures();
        Queue::fake();
    }

    public function test_valid_order_is_stored_and_queued_for_telegram(): void
    {
        $product = Product::query()->where('slug', 'male-light')->first();

        $response = $this->postJson('/order', [
            'product_id' => $product->id,
            'name' => 'Олексій',
            'phone' => '097 477 29 19',
        ]);

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
            'name' => 'Олексій',
            'phone' => '+380974772919',
            'status' => OrderStatus::New->value,
        ]);

        Queue::assertPushed(SendOrderToTelegram::class);
    }

    public function test_honeypot_returns_success_without_creating_order(): void
    {
        $response = $this->postJson('/order', [
            'name' => 'Bot',
            'phone' => '0974772919',
            'website' => 'https://spam.example',
        ]);

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseCount('orders', 0);
        Queue::assertNothingPushed();
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $response = $this->postJson('/order', [
            'name' => 'Тест',
            'phone' => '12345',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_inactive_product_cannot_be_ordered(): void
    {
        $hidden = Product::query()->where('slug', 'hidden-set')->first();

        $response = $this->postJson('/order', [
            'product_id' => $hidden->id,
            'name' => 'Тест',
            'phone' => '0974772919',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_order_without_product_is_allowed(): void
    {
        $response = $this->postJson('/order', [
            'name' => 'Гість',
            'phone' => '+380974772919',
        ]);

        $response->assertOk();

        $order = Order::query()->first();
        $this->assertNull($order->product_id);
        $this->assertSame('Гість', $order->name);
    }
}