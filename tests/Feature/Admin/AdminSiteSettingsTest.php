<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\SiteSettings;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdmin;
use Tests\TestCase;

class AdminSiteSettingsTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;

    public function test_admin_can_save_site_settings(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(SiteSettings::class)
            ->fillForm([
                'support_phone' => '380501112233',
                'instagram_url' => 'https://instagram.com/test_shop',
                'tiktok_url' => '',
                'telegram_url' => 'https://t.me/test_channel',
                'og_description' => 'Тестовий опис для месенджерів.',
                'about_text' => "Абзац один.\n\nАбзац два.",
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('380501112233', Setting::get('support_phone'));
        $this->assertSame('Тестовий опис для месенджерів.', Setting::get('og_description'));
        $this->assertSame('https://instagram.com/test_shop', Setting::get('instagram_url'));
        $this->assertSame('https://t.me/test_channel', Setting::get('telegram_url'));
        $this->assertNull(Setting::get('tiktok_url'));
    }

    public function test_saved_social_settings_reflect_on_landing(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(SiteSettings::class)
            ->fillForm([
                'support_phone' => '380671112233',
                'instagram_url' => 'https://instagram.com/only_ig',
                'tiktok_url' => '',
                'telegram_url' => '',
                'about_text' => 'Текст про баню.',
            ])
            ->call('save');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://instagram.com/only_ig', false);
        $response->assertSee('380671112233', false);
        $response->assertDontSee('aria-label="TikTok"', false);
        $response->assertDontSee('aria-label="Telegram"', false);
    }

    public function test_saved_og_description_reflects_on_landing_meta_tags(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(SiteSettings::class)
            ->fillForm([
                'support_phone' => '380671112233',
                'instagram_url' => '',
                'tiktok_url' => '',
                'telegram_url' => '',
                'og_description' => 'Опис превʼю з адмінки.',
                'about_text' => 'Текст про баню.',
            ])
            ->call('save');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('property="og:description" content="Опис превʼю з адмінки."', false);
        $response->assertSee('name="twitter:description" content="Опис превʼю з адмінки."', false);
    }
}