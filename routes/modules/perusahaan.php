<?php

use App\Http\Controllers\PerusahaanController;
use Illuminate\Support\Facades\Route;

Route::prefix('perusahaan')->group(function () {
    Route::get('/', [PerusahaanController::class, 'index']);
    Route::post('/', [PerusahaanController::class, 'store']);
    Route::get('/{id}', [PerusahaanController::class, 'show']);
    Route::put('/{id}', [PerusahaanController::class, 'update']);
    Route::delete('/{id}', [PerusahaanController::class, 'destroy']);
});
