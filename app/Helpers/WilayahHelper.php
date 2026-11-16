<?php

namespace App\Helpers;

class WilayahHelper
{
    /**
     * Daftar wilayah pesisir se-Indonesia yang dikelompokkan berdasarkan zona/wilayah.
     */
    public const ZONA_WILAYAH = [
        'Jawa Timur (Fokus Pesisir Surabaya & Sekitarnya)' => [
            'Pantai Kenjeran' => ['lat' => -7.2275000, 'lng' => 112.7985000],
            'Kedung Cowek' => ['lat' => -7.2140000, 'lng' => 112.7870000],
            'Pantai Ria Kenjeran' => ['lat' => -7.2420000, 'lng' => 112.8020000],
            'Tambak Wedi' => ['lat' => -7.2030000, 'lng' => 112.7750000],
            'Sukolilo Baru' => ['lat' => -7.2340000, 'lng' => 112.7960000],
            'Pantai Balekambang (Malang)' => ['lat' => -8.4034000, 'lng' => 112.5393000],
            'Pantai Klayar (Pacitan)' => ['lat' => -8.2256000, 'lng' => 110.9472000],
            'Pantai Pulau Merah (Banyuwangi)' => ['lat' => -8.5994000, 'lng' => 114.0267000],
        ],
        'DKI Jakarta, Banten & Jawa Barat' => [
            'Kepulauan Seribu (DKI Jakarta)' => ['lat' => -5.6122000, 'lng' => 106.5604000],
            'Pantai Ancol (DKI Jakarta)' => ['lat' => -6.1200000, 'lng' => 106.8400000],
            'Pantai Anyer (Banten)' => ['lat' => -6.0460000, 'lng' => 105.9180000],
            'Pantai Tanjung Lesung (Banten)' => ['lat' => -6.4800000, 'lng' => 105.6580000],
            'Pantai Pangandaran (Jawa Barat)' => ['lat' => -7.7011000, 'lng' => 108.6578000],
            'Pelabuhan Ratu (Sukabumi)' => ['lat' => -6.9875000, 'lng' => 106.5414000],
        ],
        'Jawa Tengah & D.I. Yogyakarta' => [
            'Kepulauan Karimunjawa (Jepara)' => ['lat' => -5.8475000, 'lng' => 110.4286000],
            'Pantai Parangtritis (Yogyakarta)' => ['lat' => -8.0250000, 'lng' => 110.3340000],
            'Pantai Indrayanti (Gunungkidul)' => ['lat' => -8.1506000, 'lng' => 110.6125000],
        ],
        'Bali & Nusa Tenggara' => [
            'Pantai Kuta (Bali)' => ['lat' => -8.7185000, 'lng' => 115.1686000],
            'Pantai Sanur (Bali)' => ['lat' => -8.6750000, 'lng' => 115.2630000],
            'Pantai Nusa Dua (Bali)' => ['lat' => -8.8020000, 'lng' => 115.2310000],
            'Pantai Pandawa (Bali)' => ['lat' => -8.8450000, 'lng' => 115.1870000],
            'Gili Trawangan (Lombok)' => ['lat' => -8.3500000, 'lng' => 116.0390000],
            'Pantai Senggigi (Lombok)' => ['lat' => -8.4975000, 'lng' => 116.0489000],
            'Labuan Bajo (Manggarai Barat, NTT)' => ['lat' => -8.4964000, 'lng' => 119.8877000],
        ],
        'Sumatera' => [
            'Pantai Iboih (Sabang, Aceh)' => ['lat' => 5.8711000, 'lng' => 95.2589000],
            'Pantai Lampuuk (Aceh Besar)' => ['lat' => 5.4853000, 'lng' => 95.2344000],
            'Pantai Tanjung Tinggi (Belitung)' => ['lat' => -2.5714000, 'lng' => 107.7125000],
            'Pantai Pasir Putih (Lampung)' => ['lat' => -5.5264000, 'lng' => 105.3444000],
        ],
        'Kalimantan & Sulawesi' => [
            'Kepulauan Derawan (Berau, Kaltim)' => ['lat' => 2.2858000, 'lng' => 118.2439000],
            'Pantai Manggar Segarasari (Balikpapan)' => ['lat' => -1.2158000, 'lng' => 116.9744000],
            'Taman Nasional Bunaken (Manado)' => ['lat' => 1.6231000, 'lng' => 124.7611000],
            'Pantai Losari (Makassar)' => ['lat' => -5.1442000, 'lng' => 119.4072000],
            'Pantai Tanjung Bira (Bulukumba)' => ['lat' => -5.6186000, 'lng' => 120.4608000],
            'Wakatobi (Sulawesi Tenggara)' => ['lat' => -5.3183000, 'lng' => 123.5939000],
        ],
        'Maluku & Papua' => [
            'Pantai Ora (Maluku Tengah)' => ['lat' => -2.9772000, 'lng' => 129.2472000],
            'Kepulauan Banda Neira (Maluku)' => ['lat' => -4.5261000, 'lng' => 129.9039000],
            'Kepulauan Raja Ampat (Papua Barat Daya)' => ['lat' => -0.5000000, 'lng' => 130.5000000],
            'Teluk Youtefa (Jayapura, Papua)' => ['lat' => -2.5931000, 'lng' => 140.7139000],
        ],
    ];

    /**
     * Daftar wilayah default penugasan pemantau lokal Surabaya.
     */
    public const DAFTAR_SURABAYA = [
        'Pantai Kenjeran',
        'Kedung Cowek',
        'Pantai Ria Kenjeran',
        'Tambak Wedi',
        'Sukolilo Baru',
    ];

    /**
     * Dapatkan seluruh wilayah pesisir dalam bentuk list flat array.
     *
     * @return array<string>
     */
    public static function allNames(): array
    {
        $names = [];
        foreach (self::ZONA_WILAYAH as $wilayahs) {
            foreach ($wilayahs as $nama => $coord) {
                $names[] = $nama;
            }
        }
        return $names;
    }

    /**
     * Dapatkan koordinat geografis acuan untuk wilayah tertentu.
     *
     * @param string|null $wilayah
     * @return array{lat: float, lng: float}|null
     */
    public static function getCoordinates(?string $wilayah): ?array
    {
        if (empty($wilayah)) {
            return null;
        }

        foreach (self::ZONA_WILAYAH as $wilayahs) {
            foreach ($wilayahs as $nama => $coord) {
                if (stripos($wilayah, $nama) !== false || stripos($nama, $wilayah) !== false) {
                    return $coord;
                }
            }
        }

        return null;
    }
}
