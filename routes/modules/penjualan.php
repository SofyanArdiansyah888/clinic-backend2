<?php

use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;

Route::prefix('penjualan')->group(function () {
    Route::get('/', [PenjualanController::class, 'index']);
    Route::post('/', [PenjualanController::class, 'store']);
    Route::get('/{id}', [PenjualanController::class, 'show']);
    Route::put('/{id}', [PenjualanController::class, 'update']);
    Route::delete('/{id}', [PenjualanController::class, 'destroy']);
    Route::patch('/{id}/status', [PenjualanController::class, 'updateStatus']);
});
