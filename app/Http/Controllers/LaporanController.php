<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Http\Requests\LaporanRequest;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Pembersihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan daftar riwayat laporan (untuk masyarakat, pemantau, atau admin).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Jika user adalah masyarakat biasa, hanya tampilkan laporan miliknya sendiri (Riwayat Laporan)
        if ($user && method_exists($user, 'isMasyarakat') && $user->isMasyarakat()) {
            $laporans = Laporan::with(['kategori', 'pembersihan'])
                ->where('id_user', $user->id)
                ->latest('waktu_laporan')
                ->paginate(10);
        } 
        // Jika pemantau wilayah, tampilkan laporan sesuai wilayah tugasnya
        elseif ($user && method_exists($user, 'isPemantau') && $user->isPemantau()) {
            $laporans = Laporan::with(['user', 'kategori', 'pembersihan'])
                ->where('wilayah', $user->wilayah_tugas)
                ->latest('waktu_laporan')
                ->paginate(10);
        } 
        // Jika Admin atau peran lainnya, tampilkan seluruh laporan dengan opsi filter pencarian jika ada
        else {
            $query = Laporan::with(['user', 'kategori', 'pembersihan']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('wilayah')) {
                $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
            }

            $laporans = $query->latest('waktu_laporan')->paginate(10);
        }

        return view('lapor.index', compact('laporans'));
    }

    /**
     * Tampilkan form buat laporan (khusus masyarakat).
     */
    public function create(): View
    {
        $kategoris = Kategori::all();
        return view('lapor.create', compact('kategoris'));
    }

    /**
     * Simpan laporan baru ke database.
     */
    public function store(LaporanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['id_user'] = auth()->id();
        $validated['waktu_laporan'] = now('Asia/Jakarta');
        $validated['status'] = Laporan::STATUS_BARU;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporan', 'public');
            $validated['foto'] = $path;
        }

        $laporan = Laporan::create($validated);

        LogAktivitasHelper::catat(auth()->id(), 'Mengirimkan laporan insiden baru di wilayah: ' . $laporan->wilayah);

        return redirect()->route('lapor')->with('success', 'Laporan berhasil dikirim dan sedang ditinjau.');
    }

    /**
     * Tampilkan detail laporan (Mockup 4).
     */
    public function show(int|string $id): View
    {
        $laporan = Laporan::with(['user', 'kategori', 'pembersihan.user'])->findOrFail($id);

        $user = auth()->user();
        if ($user && method_exists($user, 'isPemantau') && $user->isPemantau() && method_exists($user, 'canAccessWilayah') && !$user->canAccessWilayah($laporan->wilayah)) {
            abort(403, 'Akses Dibatasi: Anda terdaftar sebagai Pemantau wilayah ' . ($user->wilayah_tugas ?? '-') . ' dan hanya diizinkan mengakses laporan di wilayah tugas Anda.');
        }

        return view('lapor.show', compact('laporan'));
    }

    /**
     * Update status laporan (misal: 'dipantau', 'ditindaklanjuti').
     * Eksklusif hanya untuk Pemantau Wilayah terkait.
     */
    public function updateStatus(Request $request, int|string $id): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !method_exists($user, 'isPemantau') || !$user->isPemantau()) {
            abort(403, 'Akses Dibatasi: Hanya Pemantau Wilayah yang memiliki wewenang untuk mengubah status operasional laporan.');
        }

        $laporan = Laporan::findOrFail($id);

        if (method_exists($user, 'canAccessWilayah') && !$user->canAccessWilayah($laporan->wilayah)) {
            abort(403, 'Akses Dibatasi: Anda tidak dapat mengubah status laporan di luar wilayah tugas Anda (' . ($user->wilayah_tugas ?? '-') . ').');
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', Laporan::STATUSES),
        ]);

        $newStatus = $request->input('status');
        $laporan->update(['status' => $newStatus]);

        LogAktivitasHelper::catat(auth()->id(), 'Mengubah status laporan #' . $laporan->id_laporan . ' menjadi: ' . $newStatus);

        return back()->with('success', 'Status laporan berhasil diperbarui menjadi ' . ucfirst($newStatus) . '.');
    }

    /**
     * Konfirmasi aksi pembersihan dan tandai laporan selesai (Mockup 4).
     * Eksklusif hanya untuk Pemantau Wilayah terkait.
     */
    public function tindakLanjut(Request $request, int|string $id): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !method_exists($user, 'isPemantau') || !$user->isPemantau()) {
            abort(403, 'Akses Dibatasi: Hanya Pemantau Wilayah yang memiliki wewenang untuk menyelesaikan aksi pembersihan.');
        }

        $laporan = Laporan::findOrFail($id);

        if (method_exists($user, 'canAccessWilayah') && !$user->canAccessWilayah($laporan->wilayah)) {
            abort(403, 'Akses Dibatasi: Anda tidak dapat menyelesaikan aksi pembersihan pada laporan di luar wilayah tugas Anda (' . ($user->wilayah_tugas ?? '-') . ').');
        }

        $request->validate([
            'foto_hasil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'deskripsi_hasil' => 'nullable|string|max:1000',
        ]);

        $fotoHasilPath = null;
        if ($request->hasFile('foto_hasil')) {
            $fotoHasilPath = $request->file('foto_hasil')->store('pembersihan', 'public');
        }

        Pembersihan::create([
            'id_laporan' => $laporan->id_laporan,
            'id_user' => auth()->id(),
            'tanggal' => now()->toDateString(),
            'deskripsi_hasil' => $request->input('deskripsi_hasil') ?? 'Pembersihan area pantai telah selesai dilaksanakan oleh tim relawan/pemantau.',
            'foto_hasil' => $fotoHasilPath,
        ]);

        $laporan->update(['status' => Laporan::STATUS_SELESAI]);

        LogAktivitasHelper::catat(auth()->id(), 'Menyelesaikan aksi pembersihan untuk laporan #' . $laporan->id_laporan . ' di ' . $laporan->wilayah);

        return back()->with('success', 'Aksi pembersihan berhasil dikonfirmasi! Laporan kini berstatus Selesai.');
    }
}