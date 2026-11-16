<?php

namespace App\Services;

use App\Models\Kategori;
use App\Models\Laporan;

class PrioritasService
{
    /**
     * Hitung skor urgensi dan klasifikasikan tingkat prioritas laporan.
     *
     * @param int|string $idKategori ID Kategori sampah
     * @param string|null $deskripsi Deskripsi teks dari pelapor
     * @param float|null $latitude Koordinat latitude
     * @param float|null $longitude Koordinat longitude
     * @return array{prioritas: string, skor: int, rincian: array}
     */
    public static function hitung(
        int|string $idKategori,
        ?string $deskripsi = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): array {
        $skorKategori = self::hitungBobotKategori((int) $idKategori);
        $skorDeskripsi = self::hitungBobotDeskripsi($deskripsi);
        $skorKluster = self::hitungBobotKluster($latitude, $longitude);

        $totalSkor = min(100, $skorKategori + $skorDeskripsi + $skorKluster);

        if ($totalSkor >= 70) {
            $prioritas = Laporan::PRIORITAS_TINGGI;
        } elseif ($totalSkor >= 40) {
            $prioritas = Laporan::PRIORITAS_SEDANG;
        } else {
            $prioritas = Laporan::PRIORITAS_RENDAH;
        }

        return [
            'prioritas' => $prioritas,
            'skor' => $totalSkor,
            'rincian' => [
                'kategori' => $skorKategori,
                'deskripsi' => $skorDeskripsi,
                'kluster' => $skorKluster,
            ],
        ];
    }

    /**
     * Bobot Kategori Sampah (Maks. 50 poin).
     */
    public static function hitungBobotKategori(int $idKategori): int
    {
        $kategori = Kategori::find($idKategori);
        if (!$kategori) {
            return 20;
        }

        $nama = strtolower($kategori->nama_kategori);

        if (str_contains($nama, 'jaring') || str_contains($nama, 'ghost gear')) {
            return 50;
        }

        if (str_contains($nama, 'logam') || str_contains($nama, 'kaca') || str_contains($nama, 'b3')) {
            return 40;
        }

        if (str_contains($nama, 'plastik') || str_contains($nama, 'styrofoam')) {
            return 30;
        }

        if (str_contains($nama, 'organik')) {
            return 15;
        }

        return 20;
    }

    /**
     * Bobot Kata Kunci Urgensi pada Deskripsi (Maks. 30 poin).
     */
    public static function hitungBobotDeskripsi(?string $deskripsi): int
    {
        if (empty($deskripsi)) {
            return 0;
        }

        $teks = strtolower($deskripsi);

        $kataDarurat = [
            'terjerat', 'bangkai', 'bau menyengat', 'b3', 'limbah', 'beracun',
            'minyak', 'pecahan', 'luka', 'darurat', 'bahaya', 'tajam', 'karang rusak', 'mati'
        ];

        foreach ($kataDarurat as $kd) {
            if (str_contains($teks, $kd)) {
                return 30;
            }
        }

        $kataVolume = [
            'truk', 'gunungan', 'sangat banyak', 'menumpuk', 'menutupi',
            'berton', 'ton', 'berkarung', 'karung', 'penuh', 'luas', 'parah', 'kotor sekali'
        ];

        foreach ($kataVolume as $kv) {
            if (str_contains($teks, $kv)) {
                return 20;
            }
        }

        $kataStandar = [
            'berserakan', 'kantong', 'puntung', 'sedotan', 'botol', 'bungkus'
        ];

        foreach ($kataStandar as $ks) {
            if (str_contains($teks, $ks)) {
                return 10;
            }
        }

        return 5;
    }

    /**
     * Bobot Kepadatan / Kluster Laporan Aktif Sekitar Titik (Maks. 20 poin).
     */
    public static function hitungBobotKluster(?float $latitude, ?float $longitude): int
    {
        if ($latitude === null || $longitude === null) {
            return 0;
        }

        // Radius ~200 meter (~0.002 derajat lat/long)
        $delta = 0.002;

        try {
            $laporanSekitar = Laporan::where('status', '!=', Laporan::STATUS_SELESAI)
                ->whereBetween('latitude', [$latitude - $delta, $latitude + $delta])
                ->whereBetween('longitude', [$longitude - $delta, $longitude + $delta])
                ->count();

            if ($laporanSekitar >= 2) {
                return 20;
            } elseif ($laporanSekitar === 1) {
                return 10;
            }
        } catch (\Throwable $e) {
            return 0;
        }

        return 0;
    }
}
