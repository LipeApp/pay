<?php

namespace Lipe\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketItem extends Model
{
    protected $fillable = [
        'basket_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }
} 