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
        'photo_dark',
        'photo_light',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'contents' => 'array',
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
     * Джерела фото для боксу заданого кольору.
     * Пріоритет: завантажене в адмінці (storage) → стандартне (public webp+jpg).
     *
     * @param  'dark'|'light'  $variant
     * @return array{webp: ?string, jpg: string}
     */
    public function photoSources(string $variant): array
    {
        // 1) фото, завантажене адміністратором (одразу готовий URL, без webp)
        $upload = $this->{'photo_'.$variant} ?? $this->photo;
        if (filled($upload)) {
            return ['webp' => null, 'jpg' => asset('storage/'.$upload)];
        }

        // 2) стандартне фото з public/images (є пара .webp + .jpg)
        $base = $this->{'image_'.$variant} ?? $this->image;
        if (filled($base)) {
            return ['webp' => asset($base.'.webp'), 'jpg' => asset($base.'.jpg')];
        }

        // 3) резерв — стандартна скринька
        return ['webp' => asset('images/product-box.webp'), 'jpg' => asset('images/product-box.jpg')];
    }
}
