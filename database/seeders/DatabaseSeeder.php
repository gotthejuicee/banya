<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Адмін панелі. УВАГА: на сервері запускати seed ДО optimize,
        // інакше config-кеш зробить env() порожнім.
        // Якщо адміни вже є — не чіпаємо (email/пароль міняють в адмінці,
        // і сидер не повинен «повертати» старі креденшели чи плодити юзерів)
        if (User::query()->count() === 0) {
            User::create([
                'email' => env('ADMIN_EMAIL', 'admin@idi-v-banyu.com.ua'),
                'name' => env('ADMIN_NAME', 'Адміністратор'),
                'password' => env('ADMIN_PASSWORD', 'password'), // cast 'hashed' сам захешує
            ]);
        }

        $this->call([
            ProductSeeder::class,
        ]);

        // FAQ: дефолти з config/landing.php, тільки якщо таблиця порожня
        if (FaqItem::query()->count() === 0) {
            foreach (config('landing.faq', []) as $i => $item) {
                FaqItem::create([
                    'question' => $item['q'],
                    'answer' => implode("\n\n", $item['a']),
                    'sort' => $i + 1,
                ]);
            }
        }

        // Налаштування сайту: створюємо ключі, якщо їх ще немає
        $socials = collect(config('landing.socials', []))->keyBy('icon');
        $defaults = [
            'support_phone' => config('services.support.phone'),
            'instagram_url' => $socials->get('instagram')['url'] ?? '',
            'tiktok_url' => $socials->get('tiktok')['url'] ?? '',
            'telegram_url' => $socials->get('telegram')['url'] ?? '',
            'banner_1_image' => '',
            'banner_2_image' => '',
            'about_text' => config('landing.about'),
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
