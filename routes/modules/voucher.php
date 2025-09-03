<?php

use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::prefix('voucher')->group(function () {
    Route::get('/', [VoucherController::class, 'index']);
    Route::post('/', [VoucherController::class, 'store']);
    Route::get('/{id}', [VoucherController::class, 'show']);
    Route::put('/{id}', [VoucherController::class, 'update']);
    Route::delete('/{id}', [VoucherController::class, 'destroy']);
    Route::post('/validate', [VoucherController::class, 'validateVoucher']);
});
