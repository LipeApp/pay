<?php

use Illuminate\Support\Facades\Route;
use Lipe\Payment\Http\Controllers\ClickController;

Route::prefix('click')->group(function () {
    Route::post('prepare', [ClickController::class, 'prepare'])->name('click.prepare');
    Route::post('complete', [ClickController::class, 'complete'])->name('click.complete');
});
