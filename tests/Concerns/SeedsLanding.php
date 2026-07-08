<?php

namespace Tests\Concerns;

use App\Models\FaqItem;
use App\Models\Product;
use App\Models\Setting;

trait SeedsLanding
{
    protected function seedLandingFixtures(): void
    {
        Product::create([
            'slug' => 'male-light',
            'category' => 'male',
            'name' => 'Чоловічий набір — світлий бокс',
            'tagline' => 'Подарунковий банний набір',
            'price' => 2290,
            'image' => 'images/products/box-male-light',
            'sort' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'slug' => 'female-dark',
            'category' => 'female',
            'name' => 'Жіночий набір — темний бокс',
            'tagline' => 'Подарунковий банний набір',
            'price' => 2490,
            'image' => 'images/products/box-female-dark',
            'sort' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'slug' => 'hidden-set',
            'category' => 'male',
            'name' => 'Прихований набір',
            'price' => 999,
            'sort' => 99,
            'is_active' => false,
        ]);

        FaqItem::create([
            'question' => 'Як замовити?',
            'answer' => "Натисніть «Замовити».\n\nМи передзвонимо.",
            'sort' => 1,
            'is_active' => true,
        ]);

        FaqItem::create([
            'question' => 'Приховане питання',
            'answer' => 'Не видно',
            'sort' => 2,
            'is_active' => false,
        ]);

        Setting::set('support_phone', '380974772919');
        Setting::set('instagram_url', 'https://instagram.com/idi_v_banyu_');
        Setting::set('tiktok_url', '');
        Setting::set('telegram_url', '');
        Setting::set('about_text', "Перший абзац.\n\nДругий абзац.");
    }
}