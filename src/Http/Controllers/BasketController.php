<?php

namespace Lipe\Payment\Http\Controllers;

use Lipe\Payment\Services\BasketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BasketController extends Controller
{
    protected BasketService $basketService;

    public function __construct(BasketService $basketService)
    {
        $this->basketService = $basketService;
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $item = $this->basketService->addItem(
            $request->product_id,
            $request->quantity,
            $request->price
        );

        return response()->json($item);
    }

    public function removeItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $this->basketService->removeItem($request->product_id);

        return response()->json(['message' => 'Item removed successfully']);
    }

    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $this->basketService->updateQuantity(
            $request->product_id,
            $request->quantity
        );

        return response()->json(['message' => 'Quantity updated successfully']);
    }
} 