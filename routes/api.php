<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

// Include module routes
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/antrian.php';
require __DIR__.'/modules/pasien.php';
require __DIR__.'/modules/barang.php';
require __DIR__.'/modules/treatment.php';
require __DIR__.'/modules/user.php';
require __DIR__.'/modules/staff.php';
require __DIR__.'/modules/supplier.php';
require __DIR__.'/modules/appointment.php';
require __DIR__.'/modules/perawatan.php';
require __DIR__.'/modules/pembelian.php';
require __DIR__.'/modules/penjualan.php';
require __DIR__.'/modules/voucher.php';
require __DIR__.'/modules/konversi-stok.php';
require __DIR__.'/modules/produksi-barang.php';
require __DIR__.'/modules/stok-opname.php';
require __DIR__.'/modules/kartu-stok.php';
require __DIR__.'/modules/promo.php';
require __DIR__.'/modules/membership.php';
require __DIR__.'/modules/bank.php';
require __DIR__.'/modules/perusahaan.php';
