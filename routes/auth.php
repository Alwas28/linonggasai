<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SellerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('daftar-penjual', [SellerRegistrationController::class, 'create'])->name('seller.register');
    Route::post('daftar-penjual', [SellerRegistrationController::class, 'store'])->middleware('throttle:registration');

    Route::get('lupa-kata-sandi', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('lupa-kata-sandi', [PasswordResetController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:6,1')->name('password.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
