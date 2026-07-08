<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdmin;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $this->get('/admin/site-settings')->assertRedirect('/admin/login');
    }

    public function test_guest_can_view_login_page(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_authenticated_admin_can_access_panel_pages(): void
    {
        $admin = $this->createAdmin();

        $routes = [
            '/admin',
            '/admin/orders',
            '/admin/products',
            '/admin/products/create',
            '/admin/faq-items',
            '/admin/site-settings',
            '/admin/users',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)->get($route)->assertOk();
        }
    }

    public function test_user_can_access_filament_panel(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->canAccessPanel(filament()->getPanel('admin')));
    }
}