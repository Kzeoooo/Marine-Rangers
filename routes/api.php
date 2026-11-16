<?php

use App\Http\Controllers\Api\ApiLaporanController;
use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint publik dan terautentikasi untuk aplikasi seluler, Leaflet spasial,
| dan dashboard analitik data pesisir Marine Rangers.
|
*/

Route::get('/laporan', [ApiLaporanController::class, 'index'])->name('api.laporan');
Route::get('/statistik', [StatistikController::class, 'apiStatistik'])->name('api.statistik');
