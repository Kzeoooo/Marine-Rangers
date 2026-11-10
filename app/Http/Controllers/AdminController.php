<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\LogAktivitas;
use App\Models\Pembersihan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan panel dashboard admin.
     */
    public function index(Request $request): View
    {
        $totalLaporan = Laporan::count();
        $laporanBaru = Laporan::where('status', Laporan::STATUS_BARU)->count();
        $laporanSelesai = Laporan::where('status', Laporan::STATUS_SELESAI)->count();
        $totalUsers = User::count();

        // Laporan Terbaru
        $laporans = Laporan::with(['user', 'kategori'])
            ->latest('waktu_laporan')
            ->paginate(15);

        // Log Aktivitas Terbaru
        $logs = LogAktivitas::with('user')
            ->latest('waktu')
            ->take(10)
            ->get();

        // Rekap Pengguna per Role
        $users = User::latest()->take(10)->get();

        return view('admin.index', compact(
            'totalLaporan',
            'laporanBaru',
            'laporanSelesai',
            'totalUsers',
            'laporans',
            'logs',
            'users'
        ));
    }
}
