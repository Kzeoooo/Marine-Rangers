<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Daftar wilayah pesisir standar untuk referensi wilayah tugas pemantau.
     */
    public const DAFTAR_WILAYAH = [
        'Pantai Kenjeran',
        'Kedung Cowek',
        'Pantai Ria Kenjeran',
        'Tambak Wedi',
        'Sukolilo Baru',
    ];

    /**
     * Tampilkan daftar akun pengguna dengan fitur pencarian dan filter peran.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $roleFilter = $request->input('role');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organisasi', 'like', "%{$search}%")
                  ->orWhere('wilayah_tugas', 'like', "%{$search}%");
            });
        }

        if (!empty($roleFilter) && in_array($roleFilter, User::ROLES, true)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('id_user', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik Pengguna
        $totalUsers = User::count();
        $totalAdmin = User::where('role', User::ROLE_ADMIN)->count();
        $totalPemantau = User::where('role', User::ROLE_PEMANTAU)->count();
        $totalMasyarakat = User::where('role', User::ROLE_MASYARAKAT)->count();

        $daftarWilayah = self::DAFTAR_WILAYAH;

        return view('admin.users.index', compact(
            'users',
            'search',
            'roleFilter',
            'totalUsers',
            'totalAdmin',
            'totalPemantau',
            'totalMasyarakat',
            'daftarWilayah'
        ));
    }

    /**
     * Simpan pengguna baru ke dalam database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
            'organisasi' => ['nullable', 'string', 'max:100'],
            'wilayah_tugas' => ['nullable', 'string', 'max:100'],
        ], [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Pilihan peran tidak valid.',
        ]);

        // Jika bukan pemantau, wilayah tugas dikosongkan
        $wilayahTugas = ($validated['role'] === User::ROLE_PEMANTAU) 
            ? ($validated['wilayah_tugas'] ?? null) 
            : null;

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Otomatis di-hash oleh cast 'password' => 'hashed'
            'role' => $validated['role'],
            'organisasi' => $validated['organisasi'] ?? null,
            'wilayah_tugas' => $wilayahTugas,
        ]);

        LogAktivitasHelper::catat(
            auth()->id(),
            "Menambahkan pengguna baru: {$user->nama} ({$user->role})" . ($user->wilayah_tugas ? " - {$user->wilayah_tugas}" : '')
        );

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna {$user->nama} berhasil dibuat.");
    }

    /**
     * Perbarui data akun pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
            'organisasi' => ['nullable', 'string', 'max:100'],
            'wilayah_tugas' => ['nullable', 'string', 'max:100'],
        ], [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Pilihan peran tidak valid.',
        ]);

        // Mencegah admin mendegradasi perannya sendiri agar tidak terjadi lockout
        if ($user->id_user === auth()->id() && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri dari Administrator.');
        }

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->organisasi = $validated['organisasi'] ?? null;
        $user->wilayah_tugas = ($validated['role'] === User::ROLE_PEMANTAU) 
            ? ($validated['wilayah_tugas'] ?? null) 
            : null;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        LogAktivitasHelper::catat(
            auth()->id(),
            "Memperbarui data pengguna: {$user->nama} (ID: #{$user->id_user})"
        );

        return redirect()->route('admin.users.index')->with('success', "Data pengguna {$user->nama} berhasil diperbarui.");
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Mencegah admin menghapus akunnya sendiri
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->nama;
        $idUser = $user->id_user;

        LogAktivitasHelper::catat(
            auth()->id(),
            "Menghapus akun pengguna: {$nama} (ID: #{$idUser})"
        );

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna {$nama} berhasil dihapus.");
    }
}
