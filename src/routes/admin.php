<?php

use Illuminate\Support\Facades\Route;
use gateways\src\Http\Controllers\Admin\PaymentTransactionController;
use gateways\src\Http\Controllers\Admin\AuthController;
use gateways\src\Http\Middleware\AdminAuthMiddleware;

Route::prefix('admin')->name('admin.')->group(function () {
    // Маршруты авторизации
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Защищенные маршруты
    Route::middleware([AdminAuthMiddleware::class])->group(function () {
        Route::get('transactions', [PaymentTransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}', [PaymentTransactionController::class, 'show'])->name('transactions.show');
    });
});
