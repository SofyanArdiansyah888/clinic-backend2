<?php

use App\Http\Controllers\AntrianController;
use Illuminate\Support\Facades\Route;

Route::prefix('antrians')->group(function () {
    Route::get('/', [AntrianController::class, 'index']);
    Route::post('/', [AntrianController::class, 'store']);
    Route::get('/{id}', [AntrianController::class, 'show']);
    Route::put('/{id}', [AntrianController::class, 'update']);
    Route::delete('/{id}', [AntrianController::class, 'destroy']);
});
