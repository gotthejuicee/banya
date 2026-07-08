<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_photo_uses_public_image_pair_when_no_upload(): void
    {
        $product = Product::create([
            'slug' => 'test-box',
            'category' => 'male',
            'name' => 'Test',
            'price' => 1000,
            'image' => 'images/products/box-male-light',
            'is_active' => true,
        ]);

        $photo = $product->cardPhoto();

        $this->assertStringContainsString('box-male-light.webp', $photo['webp']);
        $this->assertStringContainsString('box-male-light.png', $photo['fallback']);
    }

    public function test_card_photo_falls_back_to_default_box_without_image(): void
    {
        $product = Product::create([
            'slug' => 'no-image',
            'category' => 'female',
            'name' => 'Test',
            'price' => 1000,
            'is_active' => true,
        ]);

        $photo = $product->cardPhoto();

        $this->assertStringContainsString('product-box.webp', $photo['webp']);
        $this->assertStringContainsString('product-box.jpg', $photo['fallback']);
    }
}