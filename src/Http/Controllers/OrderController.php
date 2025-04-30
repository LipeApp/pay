<?php

namespace Lipe\Payment\Http\Controllers;

use Lipe\Payment\Models\Basket;
use Lipe\Payment\Services\BasketService;
use Lipe\Payment\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected BasketService $basketService;
    protected OrderService $orderService;

    public function __construct(
        BasketService $basketService,
        OrderService $orderService
    ) {
        $this->basketService = $basketService;
        $this->orderService = $orderService;
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'gateway' => 'required|string|in:click',
        ]);

        $basket = $this->basketService->getOrCreateBasket();
        
        if ($basket->items->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста'], 400);
        }

        $order = $this->orderService->createOrder($basket, $request->gateway);

        return response()->json([
            'order_id' => $order->id,
            'payment_url' => $order->payment_url,
        ]);
    }
} 