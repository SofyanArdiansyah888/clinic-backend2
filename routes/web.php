<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json(['message' => 'Clinic Backend API']);
});

// API Routes
Route::prefix('api')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json(['status' => 'healthy']);
    });
    
    // Include module routes
    require __DIR__.'/api.php';
});
