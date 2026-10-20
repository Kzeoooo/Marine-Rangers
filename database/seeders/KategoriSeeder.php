<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            'Plastik',
            'Jaring/Ghost gear',
            'Organik',
            'Kaca',
            'Logam',
            'Lainnya',
        ];

        foreach ($kategoriList as $nama) {
            Kategori::firstOrCreate([
                'nama_kategori' => $nama,
            ]);
        }
    }
}

