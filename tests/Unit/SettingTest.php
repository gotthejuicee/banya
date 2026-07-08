<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_persists_value_and_clears_cache(): void
    {
        Setting::set('instagram_url', 'https://instagram.com/test');

        Cache::forget('setting.instagram_url');
        Cache::rememberForever('setting.instagram_url', fn () => 'stale');

        Setting::set('instagram_url', 'https://instagram.com/updated');

        $this->assertSame('https://instagram.com/updated', Setting::get('instagram_url'));
    }

    public function test_empty_value_falls_back_to_default(): void
    {
        Setting::set('tiktok_url', '');

        $this->assertSame('fallback', Setting::get('tiktok_url', 'fallback'));
    }
}