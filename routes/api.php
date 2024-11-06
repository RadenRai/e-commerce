<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Customers\ProductController;

// Rute untuk otentikasi
Route::prefix('auth')->group(function () {
    // Rute untuk login
    Route::post('login', [AuthController::class, 'login']);

    // Rute untuk registrasi
    Route::post('register', [AuthController::class, 'register']);

    // Rute untuk reset password
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

    // Rute untuk logout dengan middleware 'auth:sanctum'
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

// Rute untuk orders dengan middleware 'auth:sanctum'
Route::prefix(prefix: 'orders')->middleware('auth:sanctum')->group(function () {
    Route::get(uri: '/', action: [OrdersController::class, 'index']);
    Route::get(uri: '/{id}', action: [OrdersController::class, 'show']);
    Route::post('/create', [OrdersController::class, 'store']);; 
});

// Rute untuk produk (dengan middleware 'auth:sanctum' jika perlu)
Route::prefix('products')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});
