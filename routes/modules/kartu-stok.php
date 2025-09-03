<?php

use App\Http\Controllers\KartuStokController;
use Illuminate\Support\Facades\Route;

Route::prefix('kartu-stok')->group(function () {
    Route::get('/', [KartuStokController::class, 'index']);
    Route::post('/', [KartuStokController::class, 'store']);
    Route::get('/{id}', [KartuStokController::class, 'show']);
    Route::delete('/{id}', [KartuStokController::class, 'destroy']);
    Route::get('/barang/{barangId}', [KartuStokController::class, 'getByBarang']);
});
