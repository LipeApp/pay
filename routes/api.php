<?php

use Illuminate\Support\Facades\Route;
use Lipe\Payment\Http\Controllers\PaymentController;
use Lipe\Payment\Http\Controllers\BasketController;
use Lipe\Payment\Http\Controllers\OrderController;

Route::prefix('payments')->group(function () {
    Route::post('/create', [PaymentController::class, 'create']);
    Route::post('/{gateway}/callback', [PaymentController::class, 'callback']);
    Route::get('/{gateway}/status/{paymentId}', [PaymentController::class, 'status']);
});

Route::prefix('basket')->group(function () {
    Route::post('/add', [BasketController::class, 'addItem']);
    Route::post('/remove', [BasketController::class, 'removeItem']);
    Route::post('/update-quantity', [BasketController::class, 'updateQuantity']);
});

Route::prefix('orders')->group(function () {
    Route::post('/create', [OrderController::class, 'create']);
}); 