<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman Beranda publik Marine Rangers.
     */
    public function index(): View
    {
        // 3 Laporan terbaru dari database
        $laporanTerbaru = Laporan::with('kategori')
            ->latest('waktu_laporan')
            ->take(3)
            ->get();

        // Tiga statistik nyata dari database (tanpa angka karangan)
        $totalLaporan = Laporan::count();
        $totalSelesai = Laporan::where('status', Laporan::STATUS_SELESAI)->count();
        $totalWilayah = Laporan::distinct('wilayah')->count('wilayah');

        return view('welcome', compact(
            'laporanTerbaru',
            'totalLaporan',
            'totalSelesai',
            'totalWilayah'
        ));
    }
}
