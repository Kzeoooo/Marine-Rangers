<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class, // Master kategori sampah pesisir
            UserSeeder::class,     // Akun demo Admin, Pemantau Daerah, & Relawan
        ]);

        // LaporanSeeder hanya dieksekusi pada lingkungan testing untuk memenuhi automated tests.
        // Di lingkungan lokal/production, database diawali dalam kondisi bersih (0 laporan dummy).
        if (app()->environment('testing')) {
            $this->call(LaporanSeeder::class);
        }
    }
}
