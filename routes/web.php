<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\ApiLaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\StatistikController;
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

// Halaman Dashboard Statistik (Terbuka untuk Umum & Terautentikasi)
Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');

// Endpoint API dengan Session Support untuk Frontend Interaktif
Route::prefix('api')->group(function () {
    Route::get('/laporan', [ApiLaporanController::class, 'index']);
    Route::get('/statistik', [StatistikController::class, 'apiStatistik']);
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

    // Daftar / Riwayat Laporan (Mengakses LaporanController@index)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Halaman Lapor (Khusus Masyarakat)
    Route::middleware('role:masyarakat')->group(function () {
        Route::get('/lapor', [LaporanController::class, 'create'])->name('lapor');
        Route::post('/lapor', [LaporanController::class, 'store'])->name('lapor.store');
    });

    // Halaman Peta (Semua pengguna yang sudah login: masyarakat, pemantau, admin)
    Route::get('/peta', [PetaController::class, 'index'])->name('peta');

    // Detail, Edit, Update, dan Hapus Laporan
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{id}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::post('/laporan/{id}/prioritas', [LaporanController::class, 'updatePrioritas'])->name('laporan.update_prioritas');

    // Aksi Pembersihan dan Update Status (Eksklusif Pemantau Wilayah)
    Route::middleware('role:pemantau')->group(function () {
        Route::post('/laporan/{id}/status', [LaporanController::class, 'updateStatus'])->name('laporan.update_status');
        Route::post('/laporan/{id}/tindaklanjut', [LaporanController::class, 'tindakLanjut'])->name('laporan.tindaklanjut');
    });

    // Halaman Admin & Manajemen Pengguna (Khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');

        // Kelola Akun Pengguna
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
            Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
            Route::put('/pengguna/{id}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/pengguna/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
    });
});