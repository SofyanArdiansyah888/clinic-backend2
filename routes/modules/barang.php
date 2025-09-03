<?php

use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::prefix('barang')->group(function () {
    Route::get('/', [BarangController::class, 'index']);
    Route::post('/', [BarangController::class, 'store']);
    Route::get('/{id}', [BarangController::class, 'show']);
    Route::put('/{id}', [BarangController::class, 'update']);
    Route::delete('/{id}', [BarangController::class, 'destroy']);
    Route::patch('/{id}/stock', [BarangController::class, 'updateStock']);
});
