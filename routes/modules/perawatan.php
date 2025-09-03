<?php

use App\Http\Controllers\PerawatanController;
use Illuminate\Support\Facades\Route;

Route::prefix('perawatan')->group(function () {
    Route::get('/', [PerawatanController::class, 'index']);
    Route::post('/', [PerawatanController::class, 'store']);
    Route::get('/{id}', [PerawatanController::class, 'show']);
    Route::put('/{id}', [PerawatanController::class, 'update']);
    Route::delete('/{id}', [PerawatanController::class, 'destroy']);
    Route::patch('/{id}/status', [PerawatanController::class, 'updateStatus']);
});
