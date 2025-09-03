<?php

use App\Http\Controllers\StokOpnameController;
use Illuminate\Support\Facades\Route;

Route::prefix('stok-opname')->group(function () {
    Route::get('/', [StokOpnameController::class, 'index']);
    Route::post('/', [StokOpnameController::class, 'store']);
    Route::get('/{id}', [StokOpnameController::class, 'show']);
    Route::put('/{id}', [StokOpnameController::class, 'update']);
    Route::delete('/{id}', [StokOpnameController::class, 'destroy']);
});
