<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackVisits
{
    /** Шляхи, які не рахуємо (адмінка, службові, ассети). */
    private const SKIP_PREFIXES = [
        'admin', 'livewire', 'order', 'images', 'storage', 'vendor',
        'css', 'js', 'fonts', 'sitemap.xml', 'robots.txt', 'favicon.ico', 'up',
    ];

    /** Маркери ботів/краулерів у User-Agent. */
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|embedly|quora|pinterest|bitlybot|telegrambot|whatsapp|preview|monitor|ping|headless|lighthouse|python|curl|wget|go-http|axios|node-fetch/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->track($request, $response);
        } catch (Throwable $e) {
            // Аналітика ніколи не повинна валити сайт
            report($e);
        }

        return $response;
    }

    private function track(Request $request, Response $response): void
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return;
        }

        // лише HTML-сторінки
        $contentType = (string) $response->headers->get('Content-Type');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return;
        }

        $path = $request->path();
        foreach (self::SKIP_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return;
            }
        }

        $ua = (string) $request->userAgent();
        if ($ua === '' || preg_match(self::BOT_PATTERN, $ua)) {
            return; // бот або порожній UA — не рахуємо
        }

        // Анонімний відбиток: незворотний хеш, не зберігаємо IP/UA у відкритому вигляді
        $visitorId = sha1(config('app.key').'|'.$request->ip().'|'.$ua);
        $day = now()->toDateString();
        $path = mb_substr($path === '' ? '/' : $path, 0, 191);

        // upsert + інкремент лічильника переглядів
        $visit = Visit::firstOrNew([
            'day' => $day,
            'visitor_id' => $visitorId,
            'path' => $path,
        ]);

        if ($visit->exists) {
            $visit->newQuery()->whereKey($visit->getKey())->update([
                'hits' => DB::raw('hits + 1'),
                'updated_at' => now(),
            ]);
        } else {
            $visit->hits = 1;
            $visit->save();
        }
    }
}
