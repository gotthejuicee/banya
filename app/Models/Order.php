<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'phone',
        'comment',
        'status',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'notified_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
