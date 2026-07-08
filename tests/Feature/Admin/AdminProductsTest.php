<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdmin;
use Tests\Concerns\SeedsLanding;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;
    use SeedsLanding;

    public function test_products_list_shows_seeded_items(): void
    {
        $this->seedLandingFixtures();
        $admin = $this->createAdmin();

        $visible = Product::query()->where('is_active', true)->get();

        Livewire::actingAs($admin)
            ->test(ListProducts::class)
            ->assertCanSeeTableRecords($visible);
    }

    public function test_admin_can_create_product_with_generated_slug(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProduct::class)
            ->fillForm([
                'name' => 'Тестовий набір',
                'category' => 'male',
                'price' => 1999,
                'sort' => 5,
                'is_active' => true,
                'contents' => [
                    ['item' => 'Повстяна шапка'],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::query()->where('name', 'Тестовий набір')->first();

        $this->assertNotNull($product);
        $this->assertSame(Str::slug('Тестовий набір'), $product->slug);
        $this->assertTrue($product->is_active);
    }

    public function test_deactivating_product_hides_it_from_landing(): void
    {
        $this->seedLandingFixtures();
        $admin = $this->createAdmin();
        $product = Product::query()->where('slug', 'male-light')->first();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\Products\Pages\EditProduct::class, [
                'record' => $product->getRouteKey(),
            ])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/')
            ->assertDontSee('Чоловічий набір — світлий бокс', false);
    }
}