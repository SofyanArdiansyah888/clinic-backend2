<?php

use App\Http\Controllers\ProduksiBarangController;
use Illuminate\Support\Facades\Route;

Route::prefix('produksi-barang')->group(function () {
    Route::get('/', [ProduksiBarangController::class, 'index']);
    Route::post('/', [ProduksiBarangController::class, 'store']);
    Route::get('/{id}', [ProduksiBarangController::class, 'show']);
    Route::put('/{id}', [ProduksiBarangController::class, 'update']);
    Route::delete('/{id}', [ProduksiBarangController::class, 'destroy']);
});
