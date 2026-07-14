<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdmin;
use Tests\TestCase;

class AdminResetActionsTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->createAdmin());
    }

    public function test_dashboard_button_clears_visits_but_keeps_orders(): void
    {
        Visit::create(['day' => '2026-07-01', 'visitor_id' => 'a', 'path' => '/', 'hits' => 5]);
        Visit::create(['day' => '2026-07-02', 'visitor_id' => 'b', 'path' => '/', 'hits' => 2]);
        Order::create(['name' => 'Клієнт', 'phone' => '+380971234567', 'status' => 'new']);

        Livewire::test(Dashboard::class)
            ->callAction('clearVisits');

        $this->assertSame(0, Visit::count());
        $this->assertSame(1, Order::count(), 'Заявки чіпати не можна');
    }

    public function test_orders_button_deletes_all_orders_but_keeps_visits(): void
    {
        Order::create(['name' => 'Тест 1', 'phone' => '+380971234567', 'status' => 'new']);
        Order::create(['name' => 'Тест 2', 'phone' => '+380971234568', 'status' => 'done']);
        Visit::create(['day' => '2026-07-01', 'visitor_id' => 'a', 'path' => '/', 'hits' => 1]);

        Livewire::test(ListOrders::class)
            ->callAction('deleteAllOrders');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, Visit::count(), 'Статистику чіпати не можна');
    }

    public function test_buttons_are_hidden_when_there_is_nothing_to_delete(): void
    {
        Livewire::test(Dashboard::class)
            ->assertActionHidden('clearVisits');

        Livewire::test(ListOrders::class)
            ->assertActionHidden('deleteAllOrders');
    }
}
