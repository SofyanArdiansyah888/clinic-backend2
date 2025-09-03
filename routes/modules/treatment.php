<?php

use App\Http\Controllers\TreatmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('treatment')->group(function () {
    Route::get('/', [TreatmentController::class, 'index']);
    Route::post('/', [TreatmentController::class, 'store']);
    Route::get('/{id}', [TreatmentController::class, 'show']);
    Route::put('/{id}', [TreatmentController::class, 'update']);
    Route::delete('/{id}', [TreatmentController::class, 'destroy']);
});
