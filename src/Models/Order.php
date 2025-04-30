<?php

namespace Lipe\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'basket_id',
        'status',
        'total_amount',
        'payment_gateway',
        'payment_id',
        'payment_url',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
