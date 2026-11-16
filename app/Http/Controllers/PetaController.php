<?php

namespace App\Http\Controllers;

use App\Helpers\WilayahHelper;
use App\Models\Kategori;
use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetaController extends Controller
{
    /**
     * Koordinat acuan pusat wilayah pesisir untuk fokus peta pemantau.
     */
    protected const KOORDINAT_WILAYAH = [
        'Pantai Kenjeran' => ['lat' => -7.2275000, 'lng' => 112.7985000],
        'Kedung Cowek' => ['lat' => -7.2140000, 'lng' => 112.7870000],
        'Pantai Ria Kenjeran' => ['lat' => -7.2420000, 'lng' => 112.8020000],
        'Tambak Wedi' => ['lat' => -7.2030000, 'lng' => 112.7750000],
        'Sukolilo Baru' => ['lat' => -7.2340000, 'lng' => 112.7960000],
    ];

    /**
     * Tampilkan halaman Peta Sebaran Sampah (Mockup 5).
     * Jika pengguna adalah pemantau dengan wilayah tugas, data dibatasi hanya pada wilayahnya.
     */
    public function index(Request $request): View
    {
        $kategoris = Kategori::all();
        $user = auth()->user();

        // Cek apakah pengguna adalah pemantau dengan wilayah tugas spesifik
        $isPemantauWilayah = $user && $user->isPemantau() && !empty($user->wilayah_tugas);
        $wilayahTugasPemantau = $isPemantauWilayah ? $user->wilayah_tugas : null;

        // Query Laporan dengan Filter
        $query = Laporan::with('kategori');

        // Batasi wilayah jika pemantau
        if ($isPemantauWilayah) {
            $user->applyWilayahAccess($query);
            $wilayahFilter = $wilayahTugasPemantau;
        } else {
            // Filter Wilayah untuk Admin & Masyarakat
            $wilayahFilter = $request->input('wilayah', 'semua');
            if (!empty($wilayahFilter) && $wilayahFilter !== 'semua') {
                $query->where('wilayah', 'like', '%' . $wilayahFilter . '%');
            }
        }

        // Filter Jenis Sampah (Kategori)
        $selectedKategori = $request->input('kategori');
        if (!empty($selectedKategori) && is_array($selectedKategori)) {
            $query->whereIn('id_kategori', $selectedKategori);
        }

        // Filter Periode
        $periodeFilter = $request->input('periode', 'semua');
        if ($periodeFilter === '7_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(7));
        } elseif ($periodeFilter === '30_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(30));
        }

        // Filter Prioritas
        $prioritasFilter = $request->input('prioritas');
        if (!empty($prioritasFilter) && in_array($prioritasFilter, Laporan::PRIORITAS_LIST, true)) {
            $query->where('prioritas', $prioritasFilter);
        }

        $laporans = $query->get();

        // Query dasar untuk statistik (jika pemantau, statistik hanya untuk wilayah tugasnya)
        $statQuery = Laporan::query();
        if ($isPemantauWilayah) {
            $statQuery->where('wilayah', 'like', '%' . $wilayahTugasPemantau . '%');
        }

        // 1. Total Laporan & Tambahan Minggu Ini
        $totalLaporan = (clone $statQuery)->count();
        $laporanMingguIni = (clone $statQuery)->where('waktu_laporan', '>=', now()->subDays(7))->count();

        // 2. Wilayah Prioritas
        $prioritasWilayah = (clone $statQuery)->select('wilayah', DB::raw('count(*) as total'))
            ->where('status', '!=', Laporan::STATUS_SELESAI)
            ->groupBy('wilayah')
            ->orderByDesc('total')
            ->first();

        $namaWilayahPrioritas = $prioritasWilayah 
            ? $prioritasWilayah->wilayah 
            : ($isPemantauWilayah ? $wilayahTugasPemantau : 'Belum Ada Laporan Aktif');
        $jumlahLaporanPrioritas = $prioritasWilayah ? $prioritasWilayah->total : 0;

        // 3. Persentase Sudah Ditindaklanjuti (Dipantau, Ditindaklanjuti, atau Selesai)
        $ditindaklanjutiCount = (clone $statQuery)->whereIn('status', [
            Laporan::STATUS_DIPANTAU,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ])->count();

        $persentaseDitindaklanjuti = $totalLaporan > 0 
            ? round(($ditindaklanjutiCount / $totalLaporan) * 100) 
            : 0;

        // Siapkan Data Titik Spasial untuk Leaflet Map
        $titikLaporan = $laporans->map(function ($lap) {
            $priorityLevel = $lap->prioritas ?? 'sedang';
            $color = match($priorityLevel) {
                'tinggi' => '#d93829', // Merah (Urgensi Tinggi)
                'sedang' => '#e07a2c', // Oranye/Amber (Urgensi Sedang)
                'rendah' => '#10b981', // Hijau (Urgensi Rendah)
                default => '#e07a2c',
            };
            $priorityLabel = 'Prioritas ' . ucfirst($priorityLevel);

            return [
                'id' => $lap->id_laporan,
                'wilayah' => $lap->wilayah,
                'kategori' => $lap->kategori->nama_kategori ?? 'Sampah',
                'lat' => (float) $lap->latitude,
                'lng' => (float) $lap->longitude,
                'status' => $lap->status,
                'prioritas' => $priorityLevel,
                'skor_prioritas' => $lap->skor_prioritas,
                'color' => $color,
                'priority' => $priorityLabel,
                'deskripsi' => $lap->deskripsi ?? 'Tumpukan sampah pesisir',
                'waktu' => $lap->waktu_laporan ? $lap->waktu_laporan->diffForHumans() : 'Baru saja',
                'url' => url('/laporan/' . $lap->id_laporan),
            ];
        });

        // Daftar wilayah untuk dropdown filter
        if ($isPemantauWilayah) {
            $daftarWilayah = collect([$wilayahTugasPemantau]);
        } else {
            $existingWilayah = Laporan::select('wilayah')->distinct()->pluck('wilayah');
            $nationalWilayah = collect(WilayahHelper::allNames());
            $daftarWilayah = $existingWilayah->merge($nationalWilayah)->unique()->values();
        }

        // Koordinat default fokus peta (Fokus Indonesia / Pesisir Surabaya)
        $defaultCenter = [-7.2275, 112.7888];
        $defaultZoom = 12;

        if ($isPemantauWilayah) {
            $coord = WilayahHelper::getCoordinates($wilayahTugasPemantau) ?? (self::KOORDINAT_WILAYAH[$wilayahTugasPemantau] ?? null);
            if ($coord) {
                $defaultCenter = [$coord['lat'], $coord['lng']];
                $defaultZoom = 15;
            }
        } elseif (!empty($wilayahFilter) && $wilayahFilter !== 'semua') {
            $coord = WilayahHelper::getCoordinates($wilayahFilter) ?? (self::KOORDINAT_WILAYAH[$wilayahFilter] ?? null);
            if ($coord) {
                $defaultCenter = [$coord['lat'], $coord['lng']];
                $defaultZoom = 14;
            }
        }

        $zonaWilayah = WilayahHelper::ZONA_WILAYAH;

        return view('peta.index', compact(
            'kategoris',
            'titikLaporan',
            'totalLaporan',
            'laporanMingguIni',
            'namaWilayahPrioritas',
            'jumlahLaporanPrioritas',
            'persentaseDitindaklanjuti',
            'selectedKategori',
            'wilayahFilter',
            'periodeFilter',
            'daftarWilayah',
            'zonaWilayah',
            'isPemantauWilayah',
            'wilayahTugasPemantau',
            'defaultCenter',
            'defaultZoom'
        ));
    }
}
