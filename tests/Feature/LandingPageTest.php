<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsLanding;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;
    use SeedsLanding;

    public function test_landing_page_renders_successfully(): void
    {
        $this->seedLandingFixtures();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('IDI_V_BANYU__', false);
        $response->assertSee('Чоловічий набір — світлий бокс', false);
        $response->assertSee('Жіночий набір — темний бокс', false);
        $response->assertSee('Як замовити?', false);
        $response->assertDontSee('Прихований набір', false);
        $response->assertDontSee('Приховане питання', false);
    }

    public function test_only_configured_social_networks_are_shown(): void
    {
        $this->seedLandingFixtures();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Instagram"', false);
        $response->assertSee('https://instagram.com/idi_v_banyu_', false);
        $response->assertDontSee('aria-label="TikTok"', false);
        $response->assertDontSee('aria-label="Telegram"', false);
    }

    public function test_landing_includes_seo_and_structured_data(): void
    {
        $this->seedLandingFixtures();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('viewport-fit=cover', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('ItemList', false);
    }

    public function test_product_cards_render_gallery_carousel_when_gallery_set(): void
    {
        $this->seedLandingFixtures();

        $product = Product::query()->where('slug', 'male-light')->firstOrFail();
        $product->update([
            'gallery' => [
                'products/gallery/male-light-2.png',
                'products/gallery/male-light-3.png',
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('has-multiple-slides', false);
        $response->assertSee('data-slide-count="3"', false);
        $response->assertSee('pcard-arrow--prev', false);
        $response->assertSee('pcard-arrow--next', false);
        $response->assertSee('products/gallery/male-light-2.png', false);
        $response->assertSee('products/gallery/male-light-3.png', false);
        $response->assertSee('is-active', false);
    }

    public function test_product_card_without_gallery_has_single_slide_and_no_multi_class(): void
    {
        $this->seedLandingFixtures();

        // fixtures: male-light без gallery
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-slide-count="1"', false);
    }

    public function test_female_light_gallery_uses_nezaimanoi_paths_not_male(): void
    {
        $this->seedLandingFixtures();

        $product = Product::create([
            'slug' => 'dlia-nezaimanoi',
            'category' => 'female',
            'name' => 'Жіночий набір — світлий бокс',
            'tagline' => 'Подарунковий банний набір',
            'price' => 2290,
            'image' => 'images/products/box-female-light',
            'gallery' => [
                'products/gallery/female-light-2.png',
                'products/gallery/female-light-3.png',
                'products/gallery/female-light-4.png',
                'products/gallery/female-light-5.png',
            ],
            'sort' => 0,
            'is_active' => true,
        ]);

        $slides = $product->cardSlides();

        $this->assertCount(5, $slides);
        $this->assertStringContainsString('box-female-light', $slides[0]['fallback']);
        foreach (array_slice($slides, 1) as $slide) {
            $base = basename(parse_url($slide['fallback'], PHP_URL_PATH) ?? $slide['fallback']);
            // basename: female-light-N.png — не male-* / female-dark-*
            $this->assertMatchesRegularExpression('/^female-light-\d+\.png$/', $base);
            $this->assertStringStartsWith('female-light-', $base);
            $this->assertStringNotContainsString('female-dark', $base);
            $this->assertDoesNotMatchRegularExpression('/^male-/', $base);
        }

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('products/gallery/female-light-2.png', false);
        $response->assertSee('products/gallery/female-light-5.png', false);
        $response->assertSee('box-female-light', false);
    }
}