<?php

use App\Http\Controllers\PembelianController;
use Illuminate\Support\Facades\Route;

Route::prefix('pembelian')->group(function () {
    Route::get('/', [PembelianController::class, 'index']);
    Route::post('/', [PembelianController::class, 'store']);
    Route::get('/{id}', [PembelianController::class, 'show']);
    Route::put('/{id}', [PembelianController::class, 'update']);
    Route::delete('/{id}', [PembelianController::class, 'destroy']);
    Route::patch('/{id}/status', [PembelianController::class, 'updateStatus']);
});
