<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('laporan:clear', function () {
    \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
    \Illuminate\Support\Facades\DB::table('pembersihan')->truncate();
    \Illuminate\Support\Facades\DB::table('laporan')->truncate();
    \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    $this->info('Semua data laporan dan aksi pembersihan berhasil dibersihkan.');
})->purpose('Bersihkan semua data laporan dan aksi pembersihan');
