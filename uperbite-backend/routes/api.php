<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;


Route::post('/register', [AuthController::class, 'register']); // Mahasiswa & Dosen daftar
Route::get('/verify-email', [AuthController::class, 'verifyEmail']); // Verifikasi email
Route::post('/login', [AuthController::class, 'login']); // Login mahasiswa, dosen, penjual
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // Logout

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/warungs', [WarungController::class, 'index']);
    Route::post('/warungs', [WarungController::class, 'store']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::post('/warungs/{warung_id}/menus', [MenuController::class, 'store']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::get('/payment/{order_id}', [PaymentController::class, 'getSnapToken']);
    Route::post('/payment/callback', [PaymentController::class, 'paymentCallback']);
});

