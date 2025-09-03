<?php

use App\Http\Controllers\PasienController;
use Illuminate\Support\Facades\Route;

Route::prefix('pasien')->group(function () {
    Route::get('/', [PasienController::class, 'index']);
    Route::post('/', [PasienController::class, 'store']);
    Route::get('/{id}', [PasienController::class, 'show']);
    Route::put('/{id}', [PasienController::class, 'update']);
    Route::delete('/{id}', [PasienController::class, 'destroy']);
});
