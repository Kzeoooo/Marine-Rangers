<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetaController extends Controller
{
    /**
     * Tampilkan halaman Peta Sebaran Sampah (Mockup 5).
     */
    public function index(Request $request): View
    {
        $kategoris = Kategori::all();

        // Query Laporan dengan Filter
        $query = Laporan::with('kategori');

        // Filter Jenis Sampah (Kategori)
        $selectedKategori = $request->input('kategori');
        if (!empty($selectedKategori) && is_array($selectedKategori)) {
            $query->whereIn('id_kategori', $selectedKategori);
        }

        // Filter Wilayah
        $wilayahFilter = $request->input('wilayah');
        if (!empty($wilayahFilter) && $wilayahFilter !== 'semua') {
            $query->where('wilayah', 'like', '%' . $wilayahFilter . '%');
        }

        // Filter Periode
        $periodeFilter = $request->input('periode', 'semua');
        if ($periodeFilter === '7_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(7));
        } elseif ($periodeFilter === '30_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(30));
        }

        $laporans = $query->get();

        // 1. Total Laporan & Tambahan Minggu Ini
        $totalLaporan = Laporan::count();
        $laporanMingguIni = Laporan::where('waktu_laporan', '>=', now()->subDays(7))->count();

        // 2. Wilayah Prioritas (Wilayah dengan laporan terbanyak yang belum selesai)
        $prioritasWilayah = Laporan::select('wilayah', DB::raw('count(*) as total'))
            ->where('status', '!=', Laporan::STATUS_SELESAI)
            ->groupBy('wilayah')
            ->orderByDesc('total')
            ->first();

        $namaWilayahPrioritas = $prioritasWilayah ? $prioritasWilayah->wilayah : 'Pantai Kenjeran';
        $jumlahLaporanPrioritas = $prioritasWilayah ? $prioritasWilayah->total : 0;

        // 3. Persentase Sudah Ditindaklanjuti (Dipantau, Ditindaklanjuti, atau Selesai)
        $ditindaklanjutiCount = Laporan::whereIn('status', [
            Laporan::STATUS_DIPANTAU,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ])->count();

        $persentaseDitindaklanjuti = $totalLaporan > 0 
            ? round(($ditindaklanjutiCount / $totalLaporan) * 100) 
            : 76;

        // Siapkan Data Titik Spasial untuk Leaflet Map
        $titikLaporan = $laporans->map(function ($lap) {
            // Tentukan prioritas warna sesuai mockup
            // Merah = Prioritas tinggi (baru)
            // Oranye = Sedang (dipantau / ditindaklanjuti)
            // Biru = Dipantau / Selesai
            $color = '#d93829'; // default red
            $priorityLabel = 'Prioritas tinggi';

            if ($lap->status === Laporan::STATUS_BARU) {
                $color = '#d93829'; // Red
                $priorityLabel = 'Prioritas tinggi';
            } elseif ($lap->status === Laporan::STATUS_DITINDAKLANJUTI) {
                $color = '#e07a2c'; // Orange
                $priorityLabel = 'Sedang';
            } else {
                $color = '#026670'; // Teal/Blue
                $priorityLabel = 'Dipantau';
            }

            return [
                'id' => $lap->id_laporan,
                'wilayah' => $lap->wilayah,
                'kategori' => $lap->kategori->nama_kategori ?? 'Sampah',
                'lat' => (float) $lap->latitude,
                'lng' => (float) $lap->longitude,
                'status' => $lap->status,
                'color' => $color,
                'priority' => $priorityLabel,
                'deskripsi' => $lap->deskripsi ?? 'Tumpukan sampah pesisir',
                'waktu' => $lap->waktu_laporan ? $lap->waktu_laporan->diffForHumans() : 'Baru saja',
                'url' => url('/laporan/' . $lap->id_laporan),
            ];
        });

        // Daftar wilayah unik untuk dropdown
        $daftarWilayah = Laporan::select('wilayah')->distinct()->pluck('wilayah');

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
            'daftarWilayah'
        ));
    }
}
