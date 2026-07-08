<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Support\OpenGraph;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenGraphTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_bundled_og_image_and_config_description(): void
    {
        $meta = OpenGraph::meta();

        $this->assertStringContainsString('/images/og.jpg', $meta['url']);
        $this->assertSame('image/jpeg', $meta['type']);
        $this->assertSame(config('landing.og_description'), $meta['description']);
    }

    public function test_uses_uploaded_image_and_custom_description_from_settings(): void
    {
        Setting::set('og_image', 'seo/custom-og.png');
        Setting::set('og_description', 'Мій кастомний опис для Telegram.');

        $meta = OpenGraph::meta();

        $this->assertStringContainsString('/storage/seo/custom-og.png', $meta['url']);
        $this->assertSame('image/png', $meta['type']);
        $this->assertSame('Мій кастомний опис для Telegram.', $meta['description']);
    }
}