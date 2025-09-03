<?php

use App\Http\Controllers\CabangController;
use Illuminate\Support\Facades\Route;

Route::prefix('cabang')->group(function () {
    Route::get('/', [CabangController::class, 'index']);
    Route::post('/', [CabangController::class, 'store']);
    Route::get('/{id}', [CabangController::class, 'show']);
    Route::put('/{id}', [CabangController::class, 'update']);
    Route::delete('/{id}', [CabangController::class, 'destroy']);
});
