<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

// Прийом заявки: не більше 6 спроб за хвилину з однієї IP — захист від спаму/флуду.
Route::post('/order', [OrderController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('order.store');

// Карта сайту для пошукових систем
Route::get('/sitemap.xml', function () {
    $url = url('/');
    $lastmod = now()->toDateString();

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{$url}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
</urlset>
XML;

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');
