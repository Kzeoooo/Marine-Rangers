<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Http\Requests\LaporanRequest;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan form buat laporan (khusus masyarakat).
     */
    public function create(): View
    {
        return view('lapor.create'); // Nanti kita buat view-nya atau sesuaikan dengan placeholder
    }

    /**
     * Simpan laporan baru ke database.
     */
    public function store(LaporanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['id_user'] = auth()->id();
        $validated['waktu_laporan'] = now('Asia/Jakarta');
        $validated['status'] = Laporan::STATUS_BARU; // Menggunakan konstanta dari model

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporan', 'public');
            $validated['foto'] = $path;
        }

        $laporan = Laporan::create($validated);

        LogAktivitasHelper::catat(auth()->id(), 'Mengirimkan laporan insiden baru di wilayah: ' . $laporan->wilayah);

        return redirect()->route('lapor')->with('success', 'Laporan berhasil dikirim dan sedang ditinjau.');
    }
}