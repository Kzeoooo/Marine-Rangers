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
            // LaporanSeeder::class // Dinonaktifkan: database diawali dalam kondisi bersih (0 laporan dummy)
        ]);
    }
}
