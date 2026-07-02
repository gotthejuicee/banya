<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

        $faq = config('landing.faq');

        return view('landing', [
            'maleProducts' => $products->get('male', collect()),
            'femaleProducts' => $products->get('female', collect()),
            'supportPhone' => config('services.support.phone'),
            'cardFeatures' => config('landing.card_features'),
            'faq' => $faq,
            'socials' => config('landing.socials'),
            'faqJsonLd' => $this->faqJsonLd($faq),
        ]);
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
