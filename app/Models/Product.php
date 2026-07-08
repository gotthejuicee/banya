<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'name',
        'tagline',
        'description',
        'contents',
        'price',
        'old_price',
        'badge',
        'image',
        'image_dark',
        'image_light',
        'photo',
        'gallery',
        'photo_dark',
        'photo_light',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'contents' => 'array',
            'gallery' => 'array',
            'price' => 'integer',
            'old_price' => 'integer',
            'sort' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Фото картки. Пріоритет: завантажене в адмінці (storage) →
     * стандартне з public (пара webp + png/jpg) → резервна скринька.
     *
     * @return array{webp: ?string, fallback: string}
     */
    public function cardPhoto(): array
    {
        if (filled($this->photo)) {
            return ['webp' => null, 'fallback' => asset('storage/'.$this->photo)];
        }

        if (filled($this->image)) {
            // нові фото коробок — пара .webp/.png; стара скринька — .webp/.jpg
            $ext = str_contains($this->image, 'products/box-') ? 'png' : 'jpg';

            return ['webp' => asset($this->image.'.webp'), 'fallback' => asset($this->image.'.'.$ext)];
        }

        return ['webp' => asset('images/product-box.webp'), 'fallback' => asset('images/product-box.jpg')];
    }

    /**
     * Слайди каруселі картки: головне фото боксу + додаткові з галереї.
     *
     * @return list<array{webp: ?string, fallback: string, alt: string}>
     */
    public function cardSlides(): array
    {
        $main = $this->cardPhoto();
        $slides = [[
            'webp' => $main['webp'],
            'fallback' => $main['fallback'],
            'alt' => "{$this->name} — подарунковий банний набір у дерев’яному боксі",
        ]];

        foreach ($this->gallery ?? [] as $path) {
            if (! filled($path)) {
                continue;
            }

            $slides[] = [
                'webp' => null,
                'fallback' => asset('storage/'.$path),
                'alt' => "{$this->name} — вміст подарункового боксу",
            ];
        }

        return $slides;
    }
}
