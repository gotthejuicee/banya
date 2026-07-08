<?php

namespace Tests\Feature;

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
}