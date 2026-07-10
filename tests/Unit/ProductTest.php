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

    public function test_card_slides_includes_main_photo_only_without_gallery(): void
    {
        $product = Product::create([
            'slug' => 'slides-main',
            'category' => 'male',
            'name' => 'Test Box',
            'price' => 1000,
            'image' => 'images/products/box-male-light',
            'is_active' => true,
        ]);

        $slides = $product->cardSlides();

        $this->assertCount(1, $slides);
        $this->assertStringContainsString('box-male-light.png', $slides[0]['fallback']);
        $this->assertStringContainsString('Test Box', $slides[0]['alt']);
    }

    public function test_card_slides_appends_gallery_photos(): void
    {
        $product = Product::create([
            'slug' => 'slides-gallery',
            'category' => 'female',
            'name' => 'Gallery Box',
            'price' => 1200,
            'photo' => 'products/main.png',
            'gallery' => ['products/gallery/inside-1.jpg', 'products/gallery/inside-2.jpg'],
            'is_active' => true,
        ]);

        $slides = $product->cardSlides();

        $this->assertCount(3, $slides);
        $this->assertStringContainsString('storage/products/main.png', $slides[0]['fallback']);
        $this->assertStringContainsString('storage/products/gallery/inside-1.jpg', $slides[1]['fallback']);
        $this->assertStringContainsString('вміст подарункового боксу', $slides[1]['alt']);
    }

    public function test_card_slides_skips_empty_gallery_entries(): void
    {
        $product = Product::create([
            'slug' => 'slides-empty-entries',
            'category' => 'male',
            'name' => 'Sparse Gallery',
            'price' => 1000,
            'image' => 'images/products/box-male-light',
            'gallery' => ['products/gallery/a.png', '', null, 'products/gallery/b.png'],
            'is_active' => true,
        ]);

        $slides = $product->cardSlides();

        $this->assertCount(3, $slides);
        $this->assertStringContainsString('box-male-light.png', $slides[0]['fallback']);
        $this->assertStringContainsString('gallery/a.png', $slides[1]['fallback']);
        $this->assertStringContainsString('gallery/b.png', $slides[2]['fallback']);
    }

    public function test_card_slides_prefers_webp_sibling_when_file_exists(): void
    {
        $dir = storage_path('app/public/products/gallery');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $png = $dir.DIRECTORY_SEPARATOR.'_test-slide.png';
        $webp = $dir.DIRECTORY_SEPARATOR.'_test-slide.webp';
        // мінімальні валідні файли-заглушки (не зображення — is_file достатньо)
        file_put_contents($png, 'png');
        file_put_contents($webp, 'webp');

        try {
            $product = Product::create([
                'slug' => 'slides-webp',
                'category' => 'female',
                'name' => 'Webp Box',
                'price' => 1100,
                'image' => 'images/products/box-female-light',
                'gallery' => ['products/gallery/_test-slide.png'],
                'is_active' => true,
            ]);

            $slides = $product->cardSlides();

            $this->assertCount(2, $slides);
            $this->assertNotNull($slides[1]['webp']);
            $this->assertStringContainsString('products/gallery/_test-slide.webp', $slides[1]['webp']);
            $this->assertStringContainsString('products/gallery/_test-slide.png', $slides[1]['fallback']);
        } finally {
            @unlink($png);
            @unlink($webp);
        }
    }

    public function test_card_slides_webp_null_when_sibling_missing(): void
    {
        $product = Product::create([
            'slug' => 'slides-no-webp',
            'category' => 'male',
            'name' => 'No Webp',
            'price' => 1000,
            'photo' => 'products/main.png',
            'gallery' => ['products/gallery/only-png.png'],
            'is_active' => true,
        ]);

        $slides = $product->cardSlides();

        $this->assertNull($slides[1]['webp']);
        $this->assertStringContainsString('only-png.png', $slides[1]['fallback']);
    }
}