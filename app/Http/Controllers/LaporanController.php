<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Http\Requests\LaporanRequest;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Pembersihan;
use App\Services\PrioritasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan daftar riwayat laporan (untuk masyarakat, pemantau, atau admin).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Laporan::with(['user', 'kategori', 'pembersihan']);

        $availablePrioritas = Laporan::PRIORITAS_LIST;
        $selectedPrioritas = $request->input('prioritas');

        if ($request->filled('prioritas') && in_array($request->prioritas, Laporan::PRIORITAS_LIST, true)) {
            $query->where('prioritas', $request->prioritas);
        }

        // 1. Masyarakat: hanya melihat laporan miliknya sendiri
        if ($user->isMasyarakat()) {
            $query->where('id_user', $user->id_user);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $availableStatuses = Laporan::STATUSES;
            $selectedStatus = $request->input('status');
        } 
        // 2. Pemantau: hanya melihat laporan di wilayah tugasnya dengan status dipantau, ditindaklanjuti, dan selesai (BUKAN baru)
        elseif ($user->isPemantau()) {
            $allowedStatuses = [
                Laporan::STATUS_DIPANTAU,
                Laporan::STATUS_DITINDAKLANJUTI,
                Laporan::STATUS_SELESAI,
            ];

            // Terapkan batasan wilayah tugas dari User model (satu sumber aturan)
            $user->applyWilayahAccess($query);

            // Filter status: pemantau memfilter di antara status yang boleh dilihatnya
            if ($request->filled('status') && in_array($request->status, $allowedStatuses, true)) {
                $query->where('status', $request->status);
            } else {
                $query->whereIn('status', $allowedStatuses);
            }

            $availableStatuses = $allowedStatuses;
            $selectedStatus = $request->input('status');
        } 
        // 3. Admin: melihat seluruh laporan dengan filter status lengkap (baru, dipantau, ditindaklanjuti, selesai)
        else {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('wilayah')) {
                $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
            }

            $availableStatuses = Laporan::STATUSES;
            $selectedStatus = $request->input('status');
        }

        $laporans = $query->latest('waktu_laporan')->paginate(10)->withQueryString();

        $statCounts = [
            'total' => Laporan::count(),
            'baru' => Laporan::where('status', Laporan::STATUS_BARU)->count(),
            'dipantau' => Laporan::whereIn('status', [Laporan::STATUS_DIPANTAU, Laporan::STATUS_DITINDAKLANJUTI])->count(),
            'selesai' => Laporan::where('status', Laporan::STATUS_SELESAI)->count(),
        ];

        return view('lapor.index', compact('laporans', 'availableStatuses', 'selectedStatus', 'availablePrioritas', 'selectedPrioritas', 'statCounts'));
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

        // Hitung Prioritas & Skor Otomatis berdasarkan Skema Penilaian
        $hasilPrioritas = PrioritasService::hitung(
            $validated['id_kategori'],
            $validated['deskripsi'] ?? null,
            isset($validated['latitude']) ? (float) $validated['latitude'] : null,
            isset($validated['longitude']) ? (float) $validated['longitude'] : null
        );

        $validated['prioritas'] = $hasilPrioritas['prioritas'];
        $validated['skor_prioritas'] = $hasilPrioritas['skor'];

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporan', 'public');
            $validated['foto'] = $path;
        }

        $laporan = Laporan::create($validated);

        LogAktivitasHelper::catat(auth()->id(), 'Mengirimkan laporan insiden baru di wilayah: ' . $laporan->wilayah);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dikirim dan sedang ditinjau.');
    }

    /**
     * Tampilkan formulir edit laporan (khusus relawan pemilik laporan atau admin).
     * Hanya dapat diedit selama laporan berstatus 'baru'.
     */
    public function edit(int|string $id): View|RedirectResponse
    {
        $laporan = Laporan::with('kategori')->findOrFail($id);
        $user = auth()->user();

        // Verifikasi kepemilikan
        if (!$user->isAdmin() && $laporan->id_user !== $user->id_user) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit laporan milik pengguna lain.');
        }

        // Laporan hanya boleh diedit jika masih berstatus 'baru'
        if (!$laporan->isBaru()) {
            return redirect()->route('laporan.index')
                ->with('error', 'Laporan yang telah diverifikasi (dipantau) atau sedang ditindaklanjuti tidak dapat diedit kembali.');
        }

        $kategoris = Kategori::all();

        return view('lapor.edit', compact('laporan', 'kategoris'));
    }

    /**
     * Perbarui laporan insiden di database.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $laporan = Laporan::findOrFail($id);
        $user = auth()->user();

        // Verifikasi kepemilikan
        if (!$user->isAdmin() && $laporan->id_user !== $user->id_user) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui laporan milik pengguna lain.');
        }

        // Laporan hanya boleh diubah jika masih berstatus 'baru'
        if (!$laporan->isBaru()) {
            return redirect()->route('laporan.index')
                ->with('error', 'Laporan yang telah diverifikasi atau sedang ditindaklanjuti tidak dapat diubah kembali.');
        }

        $validated = $request->validate([
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'wilayah' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'id_kategori.required' => 'Kategori sampah wajib dipilih.',
            'id_kategori.exists' => 'Kategori yang dipilih tidak valid.',
            'wilayah.required' => 'Wilayah laporan wajib diisi.',
            'latitude.required' => 'Koordinat latitude wajib ada.',
            'longitude.required' => 'Koordinat longitude wajib ada.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika tersimpan di disk public
            if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        // Hitung ulang prioritas berdasarkan perubahan data
        $hasilPrioritas = PrioritasService::hitung(
            $validated['id_kategori'],
            $validated['deskripsi'] ?? null,
            isset($validated['latitude']) ? (float) $validated['latitude'] : null,
            isset($validated['longitude']) ? (float) $validated['longitude'] : null
        );

        $validated['prioritas'] = $hasilPrioritas['prioritas'];
        $validated['skor_prioritas'] = $hasilPrioritas['skor'];

        $laporan->update($validated);

        LogAktivitasHelper::catat(
            auth()->id(),
            'Memperbarui laporan insiden #' . $laporan->id_laporan . ' di wilayah: ' . $laporan->wilayah
        );

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus / batalkan laporan (khusus relawan pemilik laporan atau admin).
     * Hanya dapat dihapus selama laporan berstatus 'baru'.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $laporan = Laporan::findOrFail($id);
        $user = auth()->user();

        // Verifikasi kepemilikan
        if (!$user->isAdmin() && $laporan->id_user !== $user->id_user) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus laporan milik pengguna lain.');
        }

        // Laporan hanya boleh dihapus jika masih berstatus 'baru'
        if (!$laporan->isBaru()) {
            return redirect()->route('laporan.index')
                ->with('error', 'Laporan yang telah diverifikasi atau sedang ditindaklanjuti tidak dapat dihapus.');
        }

        $idLaporan = $laporan->id_laporan;
        $wilayah = $laporan->wilayah;

        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        LogAktivitasHelper::catat(
            auth()->id(),
            'Menghapus laporan insiden #' . $idLaporan . ' di wilayah: ' . $wilayah
        );

        return redirect()->route('laporan.index')->with('success', 'Laporan #' . $idLaporan . ' berhasil dihapus.');
    }

    /**
     * Tampilkan detail laporan (Mockup 4).
     */
    public function show(int|string $id): View
    {
        $laporan = Laporan::with(['user', 'kategori', 'pembersihan.user'])->findOrFail($id);

        $user = auth()->user();
        if ($user && $user->isPemantau() && !$user->canAccessWilayah($laporan->wilayah)) {
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
        if (!$user || !$user->isPemantau()) {
            abort(403, 'Akses Dibatasi: Hanya Pemantau Wilayah yang memiliki wewenang untuk mengubah status operasional laporan.');
        }

        $laporan = Laporan::findOrFail($id);

        if (!$user->canAccessWilayah($laporan->wilayah)) {
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
        if (!$user || !$user->isPemantau()) {
            abort(403, 'Akses Dibatasi: Hanya Pemantau Wilayah yang memiliki wewenang untuk menyelesaikan aksi pembersihan.');
        }

        $laporan = Laporan::findOrFail($id);

        if (!$user->canAccessWilayah($laporan->wilayah)) {
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

    /**
     * Kalibrasi tingkat prioritas laporan.
     * Dapat dilakukan oleh Pemantau Wilayah penugasan atau Administrator.
     */
    public function updatePrioritas(Request $request, int|string $id): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isPemantau() && !$user->isAdmin())) {
            abort(403, 'Akses Dibatasi: Hanya Pemantau Wilayah atau Administrator yang berwenang mengkalibrasi tingkat prioritas.');
        }

        $laporan = Laporan::findOrFail($id);

        if ($user->isPemantau() && !$user->canAccessWilayah($laporan->wilayah)) {
            abort(403, 'Akses Dibatasi: Anda tidak dapat mengubah prioritas laporan di luar wilayah tugas Anda (' . ($user->wilayah_tugas ?? '-') . ').');
        }

        $request->validate([
            'prioritas' => 'required|in:' . implode(',', Laporan::PRIORITAS_LIST),
        ]);

        $prioritasBaru = $request->input('prioritas');
        $laporan->update(['prioritas' => $prioritasBaru]);

        LogAktivitasHelper::catat(
            auth()->id(),
            'Mengkalibrasi tingkat prioritas laporan #' . $laporan->id_laporan . ' menjadi: ' . ucfirst($prioritasBaru)
        );

        return back()->with('success', 'Tingkat prioritas laporan berhasil diubah menjadi ' . ucfirst($prioritasBaru) . '.');
    }
}
