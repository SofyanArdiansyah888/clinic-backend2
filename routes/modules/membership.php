<?php

use App\Http\Controllers\MembershipController;
use Illuminate\Support\Facades\Route;

Route::prefix('membership')->group(function () {
    Route::get('/', [MembershipController::class, 'index']);
    Route::post('/', [MembershipController::class, 'store']);
    Route::get('/{id}', [MembershipController::class, 'show']);
    Route::put('/{id}', [MembershipController::class, 'update']);
    Route::delete('/{id}', [MembershipController::class, 'destroy']);
    Route::post('/{id}/add-points', [MembershipController::class, 'addPoints']);
    Route::post('/{id}/use-points', [MembershipController::class, 'usePoints']);
});
