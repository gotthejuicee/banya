<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    /**
     * Відповідь як масив абзаців (порожній рядок у тексті = новий абзац).
     *
     * @return list<string>
     */
    public function getParagraphsAttribute(): array
    {
        return array_values(array_filter(
            preg_split('/\R{2,}/u', trim($this->answer)) ?: [],
            fn (string $p) => $p !== '',
        ));
    }
}
