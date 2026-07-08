<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdmin;
use Tests\Concerns\SeedsLanding;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;
    use SeedsLanding;

    public function test_orders_list_shows_existing_order(): void
    {
        $this->seedLandingFixtures();
        $admin = $this->createAdmin();
        $product = Product::query()->where('slug', 'male-light')->first();

        $order = Order::create([
            'product_id' => $product->id,
            'name' => 'Петро',
            'phone' => '+380501112233',
            'status' => OrderStatus::New,
        ]);

        Livewire::actingAs($admin)
            ->test(ListOrders::class)
            ->assertCanSeeTableRecords([$order]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $this->seedLandingFixtures();
        $admin = $this->createAdmin();

        $order = Order::create([
            'name' => 'Ірина',
            'phone' => '+380671112233',
            'status' => OrderStatus::New,
        ]);

        Livewire::actingAs($admin)
            ->test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['status' => OrderStatus::InProgress->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            OrderStatus::InProgress,
            $order->fresh()->status,
        );
    }

    public function test_navigation_badge_counts_new_orders(): void
    {
        Order::create(['name' => 'A', 'phone' => '+380501111111', 'status' => OrderStatus::New]);
        Order::create(['name' => 'B', 'phone' => '+380502222222', 'status' => OrderStatus::New]);
        Order::create(['name' => 'C', 'phone' => '+380503333333', 'status' => OrderStatus::Done]);

        $this->assertSame('2', OrderResource::getNavigationBadge());
    }
}