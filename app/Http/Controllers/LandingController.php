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

        $aboutText = Setting::get('about_text', config('landing.about'));
        $aboutParagraphs = array_values(array_filter(
            preg_split('/\R{2,}/u', trim((string) $aboutText)) ?: [],
            fn (string $p) => $p !== '',
        ));

        $socials = $this->socials();
        $allProducts = $products->flatten();

        return view('landing', [
            'maleProducts' => $products->get('male', collect()),
            'femaleProducts' => $products->get('female', collect()),
            'supportPhone' => Setting::get('support_phone', config('services.support.phone')),
            'faq' => $faq,
            'aboutParagraphs' => $aboutParagraphs,
            'socials' => $socials,
            'banners' => [
                Setting::get('banner_1_image'),
                Setting::get('banner_2_image'),
            ],
            'faqJsonLd' => $this->faqJsonLd($faq),
            'siteJsonLd' => $this->siteJsonLd($socials),
            'productsJsonLd' => $this->productsJsonLd($allProducts),
        ]);
    }

    /**
     * Organization + WebSite розмітка: назва, лого (бігунець) і соцпрофілі.
     *
     * @param  list<array{name: string, icon: string, url: string}>  $socials
     */
    private function siteJsonLd(array $socials): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => url('/').'#organization',
                    'name' => 'IDI_V_BANYU__',
                    'url' => url('/'),
                    'logo' => asset('favicon-512.png'),
                    'image' => asset('images/og.jpg'),
                    'telephone' => '+'.Setting::get('support_phone', config('services.support.phone')),
                    'sameAs' => array_column($socials, 'url'),
                ],
                [
                    '@type' => 'WebSite',
                    'name' => 'IDI_V_BANYU__ — банні набори',
                    'url' => url('/'),
                    'inLanguage' => 'uk',
                    'publisher' => ['@id' => url('/').'#organization'],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Product-розмітка для кожного набору — ціни й наявність у видачі Google.
     *
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function productsJsonLd($products): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $products->values()->map(fn (Product $p, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => [
                    '@type' => 'Product',
                    'name' => $p->name,
                    'description' => trim(($p->tagline ? $p->tagline.'. ' : '').(string) $p->description),
                    'image' => $p->cardPhoto()['fallback'],
                    'brand' => ['@type' => 'Brand', 'name' => 'IDI_V_BANYU__'],
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $p->price,
                        'priceCurrency' => 'UAH',
                        'availability' => 'https://schema.org/InStock',
                        'url' => url('/').'#'.($p->category === 'male' ? 'men' : 'women'),
                    ],
                ],
            ])->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
