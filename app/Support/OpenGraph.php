<?php

namespace App\Support;

use App\Models\Setting;

class OpenGraph
{
    /**
     * @return array{url: string, secure_url: string, type: string, alt: string, description: string}
     */
    public static function meta(): array
    {
        $path = Setting::get('og_image');
        $description = Setting::get('og_description', config('landing.og_description'));

        if (filled($path)) {
            $url = asset('storage/'.$path);
            $secureUrl = secure_asset('storage/'.$path);
            $type = str_ends_with(strtolower($path), '.png') ? 'image/png' : 'image/jpeg';
        } else {
            $url = asset('images/og.jpg');
            $secureUrl = secure_asset('images/og.jpg');
            $type = 'image/jpeg';
        }

        return [
            'url' => $url,
            'secure_url' => $secureUrl,
            'type' => $type,
            'alt' => $description,
            'description' => $description,
        ];
    }
}