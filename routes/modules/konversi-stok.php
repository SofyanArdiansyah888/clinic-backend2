<?php

use App\Http\Controllers\KonversiStokController;
use Illuminate\Support\Facades\Route;

Route::prefix('konversi-stok')->group(function () {
    Route::get('/', [KonversiStokController::class, 'index']);
    Route::post('/', [KonversiStokController::class, 'store']);
    Route::get('/{id}', [KonversiStokController::class, 'show']);
    Route::put('/{id}', [KonversiStokController::class, 'update']);
    Route::delete('/{id}', [KonversiStokController::class, 'destroy']);
});
