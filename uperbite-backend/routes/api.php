<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DiscountController;


Route::post('/register', [AuthController::class, 'register']); // Mahasiswa & Dosen daftar
Route::get('/verify-email', [AuthController::class, 'verifyEmail']); // Verifikasi email
Route::post('/login', [AuthController::class, 'login']); // Login mahasiswa, dosen, penjual
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // Logout

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/warungs', [WarungController::class, 'index']);
    Route::post('/warungs', [WarungController::class, 'store']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/warungs/{id}', [WarungController::class, 'show']);
    Route::post('/warungs/{warung_id}/menus', [MenuController::class, 'store']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::get('/payment/{order_id}', [PaymentController::class, 'getSnapToken']);
    Route::post('/payment/callback', [PaymentController::class, 'paymentCallback']);

    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::post('/discounts', [DiscountController::class, 'store']);

    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    Route::get('/order-histories', [OrderHistoryController::class, 'index']);
    Route::get('/order-histories/{id}', [OrderHistoryController::class, 'show']);
});

