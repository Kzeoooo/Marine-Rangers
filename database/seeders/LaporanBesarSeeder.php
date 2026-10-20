<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaporanBesarSeeder extends Seeder
{
    /**
     * Seeder performa untuk menguji kapasitas query peta dengan 1.000 data titik.
     * Dijalankan manual dengan: php artisan db:seed --class=LaporanBesarSeeder
     */
    public function run(): void
    {
        $userIds = User::pluck('id_user')->toArray();
        $kategoriIds = Kategori::pluck('id_kategori')->toArray();

        if (empty($userIds) || empty($kategoriIds)) {
            $this->command->error('Silakan jalankan UserSeeder dan KategoriSeeder terlebih dahulu.');
            return;
        }

        $pantaiLocations = [
            ['wilayah' => 'Pantai Kenjeran', 'lat' => -7.2275000, 'lng' => 112.7985000],
            ['wilayah' => 'Kedung Cowek', 'lat' => -7.2140000, 'lng' => 112.7870000],
            ['wilayah' => 'Pantai Ria Kenjeran', 'lat' => -7.2420000, 'lng' => 112.8020000],
            ['wilayah' => 'Tambak Wedi', 'lat' => -7.2030000, 'lng' => 112.7750000],
            ['wilayah' => 'Sukolilo Baru', 'lat' => -7.2340000, 'lng' => 112.7960000],
        ];

        $statuses = [
            Laporan::STATUS_BARU,
            Laporan::STATUS_DIPANTAU,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ];

        $total = 1000;
        $batch = [];
        $now = Carbon::now('Asia/Jakarta');

        for ($i = 1; $i <= $total; $i++) {
            $loc = $pantaiLocations[array_rand($pantaiLocations)];
            $latOffset = (mt_rand(-800, 800) / 100000);
            $lngOffset = (mt_rand(-800, 800) / 100000);

            $waktu = (clone $now)->subMinutes(mt_rand(10, 43200));

            $batch[] = [
                'id_user' => $userIds[array_rand($userIds)],
                'id_kategori' => $kategoriIds[array_rand($kategoriIds)],
                'foto' => 'uploads/laporan/bench_' . (($i % 20) + 1) . '.jpg',
                'latitude' => round($loc['lat'] + $latOffset, 7),
                'longitude' => round($loc['lng'] + $lngOffset, 7),
                'wilayah' => $loc['wilayah'],
                'deskripsi' => 'Data uji performa peta spasial #' . $i . ' di wilayah ' . $loc['wilayah'],
                'waktu_laporan' => $waktu->toDateTimeString(),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $waktu->toDateTimeString(),
                'updated_at' => $waktu->toDateTimeString(),
            ];

            // Insert per batch 250 rows agar cepat dan efisien memori
            if (count($batch) === 250) {
                Laporan::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Laporan::insert($batch);
        }
    }
}

