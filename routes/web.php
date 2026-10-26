<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// Route Tamu (Hanya untuk pengguna yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route yang Membutuhkan Autentikasi (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Redirect berdasarkan role
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Halaman Lapor (Hanya Masyarakat)
    Route::middleware('role:masyarakat')->group(function () {
        Route::get('/lapor', [LaporanController::class, 'create'])->name('lapor');
        Route::post('/lapor', [LaporanController::class, 'store'])->name('lapor.store');
    });

    // Halaman Peta (Semua pengguna yang sudah login)
    Route::get('/peta', function () {
        return view('placeholder.peta');
    })->name('peta');

    // Halaman Admin (Hanya Admin)
    Route::get('/admin', function () {
        return view('placeholder.admin');
    })->middleware('role:admin')->name('admin');
});