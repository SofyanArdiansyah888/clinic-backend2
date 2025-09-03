<?php

use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;

Route::prefix('promo')->group(function () {
    Route::get('/', [PromoController::class, 'index']);
    Route::post('/', [PromoController::class, 'store']);
    Route::get('/{id}', [PromoController::class, 'show']);
    Route::put('/{id}', [PromoController::class, 'update']);
    Route::delete('/{id}', [PromoController::class, 'destroy']);
    Route::post('/validate', [PromoController::class, 'validatePromo']);
});
