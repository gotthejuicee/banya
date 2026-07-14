<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearVisitsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_force_clears_visits_but_keeps_orders(): void
    {
        Visit::create(['day' => '2026-07-01', 'visitor_id' => 'a', 'path' => '/', 'hits' => 3]);
        Visit::create(['day' => '2026-07-02', 'visitor_id' => 'b', 'path' => '/', 'hits' => 1]);
        Order::create(['name' => 'Тест', 'phone' => '+380971234567', 'status' => 'new']);

        $this->artisan('visits:clear --force')->assertSuccessful();

        $this->assertSame(0, Visit::count());
        $this->assertSame(1, Order::count(), 'Заявки чіпати не можна');
    }

    public function test_declining_confirmation_keeps_visits(): void
    {
        Visit::create(['day' => '2026-07-01', 'visitor_id' => 'a', 'path' => '/', 'hits' => 1]);

        $this->artisan('visits:clear')
            ->expectsConfirmation('Видалити 1 записів статистики? Заявки лишаться на місці.', 'no')
            ->assertFailed();

        $this->assertSame(1, Visit::count());
    }
}
