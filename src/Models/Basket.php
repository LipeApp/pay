<?php

namespace Lipe\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Basket extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'total_price',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    protected function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BasketItem::class);
    }
}
