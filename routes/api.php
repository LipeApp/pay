<?php

use Illuminate\Support\Facades\Route;
use Lipe\Payment\Http\Controllers\PaymentController;

Route::prefix('payments')->group(function () {
    Route::post('/create', [PaymentController::class, 'create']);
    Route::post('/{gateway}/callback', [PaymentController::class, 'callback']);
    Route::get('/{gateway}/status/{paymentId}', [PaymentController::class, 'status']);
}); 