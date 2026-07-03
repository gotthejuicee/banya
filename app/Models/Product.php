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
        'photo',
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
}
