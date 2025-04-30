<?php

namespace Lipe\Payment\Services;


use Lipe\Payment\Models\Basket;
use Lipe\Payment\Models\BasketItem;

class BasketService
{
    public function getOrCreateBasket(): Basket
    {
        $user = auth()->user();
        $sessionId = session()->getId();

        return Basket::firstOrCreate(
            [
                'user_id' => $user?->id,
                'session_id' => $sessionId,
            ]
        );
    }

    public function addItem(int $productId, int $quantity = 1, float $price): BasketItem
    {
        $basket = $this->getOrCreateBasket();

        $item = BasketItem::updateOrCreate(
            [
                'basket_id' => $basket->id,
                'product_id' => $productId,
            ],
            [
                'quantity' => $quantity,
                'price' => $price,
            ]
        );

        $this->updateBasketTotal($basket);

        return $item;
    }

    public function removeItem(int $productId): void
    {
        $basket = $this->getOrCreateBasket();
        $basket->items()->where('product_id', $productId)->delete();
        $this->updateBasketTotal($basket);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $basket = $this->getOrCreateBasket();
        $item = $basket->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->update(['quantity' => $quantity]);
            $this->updateBasketTotal($basket);
        }
    }

    protected function updateBasketTotal(Basket $basket): void
    {
        $total = $basket->items()->sum(\DB::raw('quantity * price'));
        $basket->update(['total_price' => $total]);
    }
}
