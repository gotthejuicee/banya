<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'day',
        'visitor_id',
        'path',
        'hits',
    ];

    protected function casts(): array
    {
        // 'day' навмисно НЕ кастимо в date — тримаємо як рядок 'Y-m-d',
        // інакше на SQLite зберігається '...00:00:00' і firstOrNew не знаходить рядок.
        return [
            'hits' => 'integer',
        ];
    }
}
