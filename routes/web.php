<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PetaController;
use App\Models\Laporan;
use Illuminate\Support\Facades\Route;

// Halaman Utama (Mockup 2: Landing Page Beranda)
Route::get('/', function () {
    try {
        $laporanTerbaru = Laporan::with('kategori')->latest('waktu_laporan')->take(3)->get();
    } catch (\Throwable $e) {
        $laporanTerbaru = collect();
    }
    return view('welcome', compact('laporanTerbaru'));
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

    // Halaman Lapor (Khusus Masyarakat)
    Route::middleware('role:masyarakat')->group(function () {
        Route::get('/lapor', [LaporanController::class, 'create'])->name('lapor');
        Route::post('/lapor', [LaporanController::class, 'store'])->name('lapor.store');
    });

    // Halaman Peta (Semua pengguna yang sudah login: masyarakat, pemantau, admin)
    Route::get('/peta', [PetaController::class, 'index'])->name('peta');

    // Detail Laporan & Tindak Lanjut (Mockup 4)
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');

    // Aksi Pembersihan dan Update Status (Khusus Pemantau & Admin)
    Route::middleware('role:pemantau,admin')->group(function () {
        Route::post('/laporan/{id}/status', [LaporanController::class, 'updateStatus'])->name('laporan.update_status');
        Route::post('/laporan/{id}/tindaklanjut', [LaporanController::class, 'tindakLanjut'])->name('laporan.tindaklanjut');
    });

    // Halaman Admin (Khusus Admin)
    Route::get('/admin', [AdminController::class, 'index'])->middleware('role:admin')->name('admin');
});
