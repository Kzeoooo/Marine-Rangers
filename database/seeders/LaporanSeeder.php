<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Pembersihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $masyarakatUsers = $users->where('role', User::ROLE_MASYARAKAT);
        $pemantauUser = $users->firstWhere('role', User::ROLE_PEMANTAU) ?? $users->first();
        $kategoris = Kategori::all();

        if ($users->isEmpty() || $kategoris->isEmpty()) {
            return;
        }

        $pantaiLocations = [
            [
                'wilayah' => 'Pantai Kenjeran',
                'lat' => -7.2275000,
                'lng' => 112.7985000,
                'deskripsi' => [
                    'Tumpukan botol dan kemasan plastik tersangkut di bebatuan pemecah ombak.',
                    'Ditemukan kantong kresek dan sisa bungkus makanan ringan mengapung di dekat bibir pantai.',
                    'Puntung rokok dan sampah sedotan plastik berserakan di sekitar gazebo pantai.',
                ],
            ],
            [
                'wilayah' => 'Kedung Cowek',
                'lat' => -7.2140000,
                'lng' => 112.7870000,
                'deskripsi' => [
                    'Jaring nelayan robek (ghost gear) tersangkut di karang dan membahayakan biota laut.',
                    'Limbah styrofoam kotak ikan dan tali rafia terdampar terbawa arus pasang.',
                    'Potongan kayu basah bercampur sampah rumah tangga mengendap di area mangrove.',
                ],
            ],
            [
                'wilayah' => 'Pantai Ria Kenjeran',
                'lat' => -7.2420000,
                'lng' => 112.8020000,
                'deskripsi' => [
                    'Pecahan botol kaca sirup dan kaca minuman membahayakan pengunjung dan hewan pesisir.',
                    'Tumpukan kaleng minuman logam berkarat terselip di sela pasir pantai.',
                    'Limbah kemasan plastik sachet menumpuk setelah acara akhir pekan.',
                ],
            ],
            [
                'wilayah' => 'Tambak Wedi',
                'lat' => -7.2030000,
                'lng' => 112.7750000,
                'deskripsi' => [
                    'Endapan sampah plastik di sekitar pilar bawah Jembatan Suramadu.',
                    'Jaring bekas dan ban bekas terdampar di lumpur muara.',
                    'Sampah popok dan plastik sekali pakai terbawa arus sungai ke laut.',
                ],
            ],
            [
                'wilayah' => 'Sukolilo Baru',
                'lat' => -7.2340000,
                'lng' => 112.7960000,
                'deskripsi' => [
                    'Sampah daun kelapa dan potongan bambu bercampur kantong plastik.',
                    'Sisa peralatan jaring dan pelampung busa rusak terdampar di perkampungan nelayan.',
                    'Wadah deterjen dan jeriken oli kapal bekas mengotori garis pantai.',
                ],
            ],
        ];

        $statuses = [
            Laporan::STATUS_BARU,
            Laporan::STATUS_BARU,
            Laporan::STATUS_DIPANTAU,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ];

        $totalLaporan = 120;

        for ($i = 1; $i <= $totalLaporan; $i++) {
            $loc = $pantaiLocations[array_rand($pantaiLocations)];
            $kategori = $kategoris->random();
            $pelapor = $masyarakatUsers->isNotEmpty() ? $masyarakatUsers->random() : $users->random();

            // Variasi acak latitude & longitude (+/- 0.004 derajad ~ 400 meter)
            $latOffset = (mt_rand(-400, 400) / 100000);
            $lngOffset = (mt_rand(-400, 400) / 100000);

            $latitude = round($loc['lat'] + $latOffset, 7);
            $longitude = round($loc['lng'] + $lngOffset, 7);

            // Waktu tersebar 30 hari terakhir
            $daysAgo = mt_rand(0, 30);
            $hoursAgo = mt_rand(1, 23);
            $waktuLaporan = Carbon::now('Asia/Jakarta')->subDays($daysAgo)->subHours($hoursAgo);

            $status = $statuses[array_rand($statuses)];
            $deskripsiTeks = $loc['deskripsi'][array_rand($loc['deskripsi'])];

            $laporan = Laporan::create([
                'id_user' => $pelapor->id_user,
                'id_kategori' => $kategori->id_kategori,
                'foto' => 'uploads/laporan/sample_pantai_' . (($i % 10) + 1) . '.jpg',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'wilayah' => $loc['wilayah'],
                'deskripsi' => $deskripsiTeks,
                'waktu_laporan' => $waktuLaporan,
                'status' => $status,
                'created_at' => $waktuLaporan,
                'updated_at' => $waktuLaporan,
            ]);

            // Jika laporan berstatus 'selesai', buatkan riwayat pembersihan terkait
            if ($status === Laporan::STATUS_SELESAI) {
                $tanggalPembersihan = (clone $waktuLaporan)->addDays(mt_rand(1, 3))->min(Carbon::now('Asia/Jakarta'));

                Pembersihan::create([
                    'id_laporan' => $laporan->id_laporan,
                    'id_user' => $pemantauUser->id_user,
                    'tanggal' => $tanggalPembersihan->toDateString(),
                    'deskripsi_hasil' => 'Aksi bersih pantai dilaksanakan bersama 15 relawan pesisir dan komunitas. Berhasil mengumpulkan sekitar ' . mt_rand(20, 85) . ' kg sampah untuk disalurkan ke bank sampah terdekat.',
                    'foto_hasil' => 'uploads/pembersihan/sample_bersih_' . (($i % 5) + 1) . '.jpg',
                    'created_at' => $tanggalPembersihan,
                    'updated_at' => $tanggalPembersihan,
                ]);
            }
        }
    }
}

