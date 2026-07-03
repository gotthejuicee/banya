<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->groupBy('category');

        $faq = FaqItem::query()
            ->published()
            ->get()
            ->map(fn (FaqItem $item) => ['q' => $item->question, 'a' => $item->paragraphs])
            ->all();

        return view('landing', [
            'maleProducts' => $products->get('male', collect()),
            'femaleProducts' => $products->get('female', collect()),
            'supportPhone' => Setting::get('support_phone', config('services.support.phone')),
            'cardFeatures' => config('landing.card_features'),
            'faq' => $faq,
            'socials' => $this->socials(),
            'banners' => [
                Setting::get('banner_1_image'),
                Setting::get('banner_2_image'),
            ],
            'faqJsonLd' => $this->faqJsonLd($faq),
        ]);
    }

    /**
     * Соцмережі з налаштувань; порожні лінки не показуємо.
     *
     * @return list<array{name: string, icon: string, url: string}>
     */
    private function socials(): array
    {
        $links = [
            ['name' => 'Instagram', 'icon' => 'instagram', 'url' => Setting::get('instagram_url')],
            ['name' => 'TikTok', 'icon' => 'tiktok', 'url' => Setting::get('tiktok_url')],
            ['name' => 'Telegram', 'icon' => 'telegram', 'url' => Setting::get('telegram_url')],
        ];

        return array_values(array_filter($links, fn (array $link) => filled($link['url'])));
    }

    /**
     * FAQPage-розмітка для пошуковиків (rich snippets у Google).
     *
     * @param  array<int, array{q: string, a: array<int, string>}>  $faq
     */
    private function faqJsonLd(array $faq): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => implode(' ', $item['a']),
                ],
            ], $faq),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
