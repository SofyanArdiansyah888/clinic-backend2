<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\KonversiStokController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PerawatanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ProduksiBarangController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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

// Generator Routes
Route::prefix('generate-number')->group(function () {
    Route::get('/', [GeneratorController::class, 'generateNumber']);
    Route::get('/keys', [GeneratorController::class, 'getAvailableKeys']);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Antrian Routes
Route::prefix('antrians')->group(function () {
    Route::get('/', [AntrianController::class, 'index']);
    Route::post('/', [AntrianController::class, 'store']);
    Route::get('/{id}', [AntrianController::class, 'show']);
    Route::put('/{id}', [AntrianController::class, 'update']);
    Route::delete('/{id}', [AntrianController::class, 'destroy']);
});

// Pasien Routes
Route::prefix('pasien')->group(function () {
    Route::get('/', [PasienController::class, 'index']);
    Route::post('/', [PasienController::class, 'store']);
    Route::post('/export', [PasienController::class, 'export']);
    Route::post('/import', [PasienController::class, 'import']);
    Route::get('/{id}', [PasienController::class, 'show']);
    Route::put('/{id}', [PasienController::class, 'update']);
    Route::delete('/{id}', [PasienController::class, 'destroy']);
});

// Barang Routes
Route::prefix('barang')->group(function () {
    Route::get('/', [BarangController::class, 'index']);
    Route::post('/', [BarangController::class, 'store']);
    Route::get('/{id}', [BarangController::class, 'show']);
    Route::put('/{id}', [BarangController::class, 'update']);
    Route::delete('/{id}', [BarangController::class, 'destroy']);
    Route::patch('/{id}/stock', [BarangController::class, 'updateStock']);
});

// Treatment Routes
Route::prefix('treatment')->group(function () {
    Route::get('/', [TreatmentController::class, 'index']);
    Route::post('/', [TreatmentController::class, 'store']);
    Route::post('/export', [TreatmentController::class, 'export']);
    Route::get('/{id}', [TreatmentController::class, 'show']);
    Route::put('/{id}', [TreatmentController::class, 'update']);
    Route::delete('/{id}', [TreatmentController::class, 'destroy']);
});

// User Routes
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::post('/export', [UserController::class, 'export']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::patch('/{id}/password', [UserController::class, 'changePassword']);
});

// Staff Routes
Route::prefix('staff')->group(function () {
    Route::get('/', [StaffController::class, 'index']);
    Route::post('/', [StaffController::class, 'store']);
    Route::post('/export', [StaffController::class, 'export']);
    Route::get('/{id}', [StaffController::class, 'show']);
    Route::put('/{id}', [StaffController::class, 'update']);
    Route::delete('/{id}', [StaffController::class, 'destroy']);
});

// Supplier Routes
Route::prefix('supplier')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::post('/export', [SupplierController::class, 'export']);
    Route::get('/{id}', [SupplierController::class, 'show']);
    Route::put('/{id}', [SupplierController::class, 'update']);
    Route::delete('/{id}', [SupplierController::class, 'destroy']);
});

// Appointment Routes
Route::prefix('appointment')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::get('/{id}', [AppointmentController::class, 'show']);
    Route::put('/{id}', [AppointmentController::class, 'update']);
    Route::delete('/{id}', [AppointmentController::class, 'destroy']);
    Route::patch('/{id}/status', [AppointmentController::class, 'updateStatus']);
});

// Perawatan Routes
Route::prefix('perawatan')->group(function () {
    Route::get('/', [PerawatanController::class, 'index']);
    Route::post('/', [PerawatanController::class, 'store']);
    Route::get('/{id}', [PerawatanController::class, 'show']);
    Route::put('/{id}', [PerawatanController::class, 'update']);
    Route::delete('/{id}', [PerawatanController::class, 'destroy']);
    Route::patch('/{id}/status', [PerawatanController::class, 'updateStatus']);
});

// Pembelian Routes
Route::prefix('pembelian')->group(function () {
    Route::get('/', [PembelianController::class, 'index']);
    Route::post('/', [PembelianController::class, 'store']);
    Route::get('/{id}', [PembelianController::class, 'show']);
    Route::put('/{id}', [PembelianController::class, 'update']);
    Route::delete('/{id}', [PembelianController::class, 'destroy']);
    Route::patch('/{id}/status', [PembelianController::class, 'updateStatus']);
});

// Penjualan Routes
Route::prefix('penjualan')->group(function () {
    Route::get('/', [PenjualanController::class, 'index']);
    Route::post('/', [PenjualanController::class, 'store']);
    Route::get('/{id}', [PenjualanController::class, 'show']);
    Route::put('/{id}', [PenjualanController::class, 'update']);
    Route::delete('/{id}', [PenjualanController::class, 'destroy']);
    Route::patch('/{id}/status', [PenjualanController::class, 'updateStatus']);
});

// Voucher Routes
Route::prefix('voucher')->group(function () {
    Route::get('/', [VoucherController::class, 'index']);
    Route::post('/', [VoucherController::class, 'store']);
    Route::get('/{id}', [VoucherController::class, 'show']);
    Route::put('/{id}', [VoucherController::class, 'update']);
    Route::delete('/{id}', [VoucherController::class, 'destroy']);
    Route::post('/validate', [VoucherController::class, 'validateVoucher']);
});

// Konversi Stok Routes
Route::prefix('konversi-stok')->group(function () {
    Route::get('/', [KonversiStokController::class, 'index']);
    Route::post('/', [KonversiStokController::class, 'store']);
    Route::get('/{id}', [KonversiStokController::class, 'show']);
    Route::put('/{id}', [KonversiStokController::class, 'update']);
    Route::delete('/{id}', [KonversiStokController::class, 'destroy']);
});

// Produksi Barang Routes
Route::prefix('produksi-barang')->group(function () {
    Route::get('/', [ProduksiBarangController::class, 'index']);
    Route::post('/', [ProduksiBarangController::class, 'store']);
    Route::get('/{id}', [ProduksiBarangController::class, 'show']);
    Route::put('/{id}', [ProduksiBarangController::class, 'update']);
    Route::delete('/{id}', [ProduksiBarangController::class, 'destroy']);
});

// Stok Opname Routes
Route::prefix('stok-opname')->group(function () {
    Route::get('/', [StokOpnameController::class, 'index']);
    Route::post('/', [StokOpnameController::class, 'store']);
    Route::get('/{id}', [StokOpnameController::class, 'show']);
    Route::put('/{id}', [StokOpnameController::class, 'update']);
    Route::delete('/{id}', [StokOpnameController::class, 'destroy']);
});

// Kartu Stok Routes
Route::prefix('kartu-stok')->group(function () {
    Route::get('/', [KartuStokController::class, 'index']);
    Route::post('/', [KartuStokController::class, 'store']);
    Route::get('/{id}', [KartuStokController::class, 'show']);
    Route::delete('/{id}', [KartuStokController::class, 'destroy']);
    Route::get('/barang/{barangId}', [KartuStokController::class, 'getByBarang']);
});

// Promo Routes
Route::prefix('promo')->group(function () {
    Route::get('/', [PromoController::class, 'index']);
    Route::post('/', [PromoController::class, 'store']);
    Route::get('/{id}', [PromoController::class, 'show']);
    Route::put('/{id}', [PromoController::class, 'update']);
    Route::delete('/{id}', [PromoController::class, 'destroy']);
    Route::post('/validate', [PromoController::class, 'validatePromo']);
});

// Membership Routes
Route::prefix('membership')->group(function () {
    Route::get('/', [MembershipController::class, 'index']);
    Route::post('/', [MembershipController::class, 'store']);
    Route::get('/{id}', [MembershipController::class, 'show']);
    Route::put('/{id}', [MembershipController::class, 'update']);
    Route::delete('/{id}', [MembershipController::class, 'destroy']);
    Route::post('/{id}/add-points', [MembershipController::class, 'addPoints']);
    Route::post('/{id}/use-points', [MembershipController::class, 'usePoints']);
});

// Bank Routes
Route::prefix('bank')->group(function () {
    Route::get('/', [BankController::class, 'index']);
    Route::post('/', [BankController::class, 'store']);
    Route::get('/{id}', [BankController::class, 'show']);
    Route::put('/{id}', [BankController::class, 'update']);
    Route::delete('/{id}', [BankController::class, 'destroy']);
});

// Perusahaan Routes
Route::prefix('perusahaan')->group(function () {
    Route::get('/', [PerusahaanController::class, 'index']);
    Route::post('/', [PerusahaanController::class, 'store']);
    Route::get('/{id}', [PerusahaanController::class, 'show']);
    Route::put('/{id}', [PerusahaanController::class, 'update']);
    Route::delete('/{id}', [PerusahaanController::class, 'destroy']);
});