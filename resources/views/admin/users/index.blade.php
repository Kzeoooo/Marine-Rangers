@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna - Marine Rangers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Header Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>Dinas Lingkungan Hidup Surabaya · Administrator</span>
            </div>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900">
                Kelola Akun Pengguna
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola hak akses, penugasan wilayah pemantau, dan data akun sistem Marine Rangers.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin') }}" class="btn-outline-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Panel Kontrol</span>
            </a>
            <button type="button" onclick="openAddModal()" class="btn-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-gradient-to-br from-sky-50/70 via-white to-white rounded-2xl border border-sky-100 border-l-4 border-l-sky-600 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-sky-700 block mb-1">Total Pengguna</span>
            <div class="font-serif-title text-3xl font-extrabold text-slate-900 tabular-nums">{{ $totalUsers }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Semua akun terdaftar</span>
        </div>

        <div class="bg-gradient-to-br from-indigo-50/70 via-white to-white rounded-2xl border border-indigo-100 border-l-4 border-l-indigo-600 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-1">Administrator</span>
            <div class="font-serif-title text-3xl font-extrabold text-indigo-700 tabular-nums">{{ $totalAdmin }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Akses penuh sistem</span>
        </div>

        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-white rounded-2xl border border-emerald-100 border-l-4 border-l-emerald-600 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">Pemantau Wilayah</span>
            <div class="font-serif-title text-3xl font-extrabold text-emerald-600 tabular-nums">{{ $totalPemantau }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Petugas lapangan pesisir</span>
        </div>

        <div class="bg-gradient-to-br from-teal-50/70 via-white to-white rounded-2xl border border-teal-100 border-l-4 border-l-teal-600 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1">Relawan Masyarakat</span>
            <div class="font-serif-title text-3xl font-extrabold text-teal-700 tabular-nums">{{ $totalMasyarakat }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Warga pelapor sampah</span>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
            <div class="flex items-center gap-2 font-semibold text-sm mb-2">
                <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Terjadi kesalahan saat memproses data:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-6 mb-8">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, email, organisasi, atau wilayah..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800 placeholder-slate-400">
                </div>

                <!-- Role Filter -->
                <div class="w-full sm:w-48">
                    <select name="role" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-700">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pemantau" {{ $roleFilter === 'pemantau' ? 'selected' : '' }}>Pemantau</option>
                        <option value="masyarakat" {{ $roleFilter === 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold">
                    Filter
                </button>
                @if(!empty($search) || !empty($roleFilter))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Daftar Akun Pengguna</h2>
            <span class="text-xs text-slate-400">{{ $users->total() }} Akun ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">ID</th>
                        <th class="py-3.5 px-4 font-semibold">Pengguna</th>
                        <th class="py-3.5 px-4 font-semibold">Peran</th>
                        <th class="py-3.5 px-4 font-semibold">Wilayah Tugas / Organisasi</th>
                        <th class="py-3.5 px-4 font-semibold">Terdaftar</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-500">
                                #{{ str_pad($user->id_user, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $user->nama }}</span>
                                            @if($user->id_user === auth()->id())
                                                <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.2 rounded font-medium">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        Administrator
                                    </span>
                                @elseif($user->isPemantau())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Pemantau
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        Masyarakat
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->isPemantau())
                                    @if($user->wilayah_tugas)
                                        <div class="font-semibold text-emerald-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $user->wilayah_tugas }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Semua Wilayah (Belum ditentukan)</span>
                                    @endif
                                    @if($user->organisasi)
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $user->organisasi }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-600">{{ $user->organisasi ?: 'Pesisir Surabaya' }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id' => $user->id_user,
                                            'nama' => $user->nama,
                                            'email' => $user->email,
                                            'role' => $user->role,
                                            'organisasi' => $user->organisasi ?? '',
                                            'wilayah_tugas' => $user->wilayah_tugas ?? '',
                                            'is_self' => $user->id_user === auth()->id()
                                        ]) }})"
                                        class="px-2.5 py-1 text-xs font-semibold text-sky-700 hover:text-sky-900 hover:bg-sky-50 rounded-lg transition-colors">
                                        Edit
                                    </button>

                                    @if($user->id_user !== auth()->id())
                                        <button type="button"
                                            onclick="openDeleteModal({{ $user->id_user }}, '{{ addslashes($user->nama) }}', '{{ $user->email }}')"
                                            class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors">
                                            Hapus
                                        </button>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold text-slate-300 cursor-not-allowed" title="Anda tidak dapat menghapus akun Anda sendiri">
                                            Hapus
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada pengguna yang cocok</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter peran Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 sm:p-6 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna Baru -->
<div id="add-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto modal-anim">
        <button type="button" onclick="closeAddModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h3 class="font-serif-title text-xl font-bold text-slate-900 mb-1">Tambah Pengguna Baru</h3>
        <p class="text-xs text-slate-500 mb-6">Daftarkan akun administrator, pemantau wilayah pesisir se-Indonesia, atau masyarakat.</p>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap *</label>
                <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="email@contoh.com"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Kata Sandi *</label>
                <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Peran Akun *</label>
                <select name="role" id="add-role-select" onchange="toggleAddWilayahField(this.value)" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-700">
                    <option value="masyarakat" {{ old('role') === 'masyarakat' ? 'selected' : '' }}>Masyarakat (Warga Pelapor)</option>
                    <option value="pemantau" {{ old('role') === 'pemantau' ? 'selected' : '' }}>Pemantau (Petugas Lapangan Wilayah)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Akses Sistem Penuh)</option>
                </select>
            </div>

            <div id="add-wilayah-container" class="space-y-1.5 {{ old('role') === 'pemantau' ? '' : 'hidden' }}">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700">
                        Wilayah Tugas Pemantau (Nusantara)
                    </label>
                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded font-medium">Seluruh Indonesia</span>
                </div>
                <select name="wilayah_tugas" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 focus:border-emerald-600 focus:ring focus:ring-emerald-200 text-xs sm:text-sm text-slate-700">
                    <option value="">-- Pilih Wilayah Tugas Pantai (Seluruh Indonesia) --</option>
                    @if(isset($zonaWilayah) && is_array($zonaWilayah))
                        @foreach($zonaWilayah as $namaZona => $pantaiList)
                            <optgroup label="{{ $namaZona }}">
                                @foreach($pantaiList as $namaPantai => $coord)
                                    <option value="{{ $namaPantai }}" {{ old('wilayah_tugas') === $namaPantai ? 'selected' : '' }}>{{ $namaPantai }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach($daftarWilayah as $dw)
                            <option value="{{ $dw }}" {{ old('wilayah_tugas') === $dw ? 'selected' : '' }}>{{ $dw }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="text-[11px] text-slate-400">Pemantau hanya dapat melihat dan menindaklanjuti laporan di wilayah pesisir yang ditugaskan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Organisasi / Komunitas (Opsional)</label>
                <input type="text" name="organisasi" value="{{ old('organisasi') }}" placeholder="Contoh: DLH Surabaya / Komunitas Peduli Pesisir"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="edit-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto modal-anim">
        <button type="button" onclick="closeEditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h3 class="font-serif-title text-xl font-bold text-slate-900 mb-1">Edit Pengguna</h3>
        <p class="text-xs text-slate-500 mb-6">Perbarui profil pengguna, peran, atau wilayah penugasan.</p>

        <form id="edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap *</label>
                <input type="text" id="edit-nama" name="nama" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Email *</label>
                <input type="email" id="edit-email" name="email" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Kata Sandi Baru
                    <span class="text-slate-400 lowercase font-normal">(kosongkan jika tidak ingin mengubah)</span>
                </label>
                <input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter baru"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Peran Akun *</label>
                <input type="hidden" id="edit-role-hidden" name="role" value="admin" disabled>
                <select name="role" id="edit-role-select" onchange="toggleEditWilayahField(this.value)" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-700">
                    <option value="masyarakat">Masyarakat (Warga Pelapor)</option>
                    <option value="pemantau">Pemantau (Petugas Lapangan Wilayah)</option>
                    <option value="admin">Administrator (Akses Sistem Penuh)</option>
                </select>
                <p id="edit-role-warning" class="hidden text-[11px] text-amber-600 mt-1">Anda tidak dapat menurunkan peran akun Anda sendiri dari Administrator.</p>
            </div>

            <div id="edit-wilayah-container" class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700">
                        Wilayah Tugas Pemantau (Nusantara)
                    </label>
                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded font-medium">Seluruh Indonesia</span>
                </div>
                <select name="wilayah_tugas" id="edit-wilayah-select" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 focus:border-emerald-600 focus:ring focus:ring-emerald-200 text-xs sm:text-sm text-slate-700">
                    <option value="">-- Pilih Wilayah Tugas Pantai (Seluruh Indonesia) --</option>
                    @if(isset($zonaWilayah) && is_array($zonaWilayah))
                        @foreach($zonaWilayah as $namaZona => $pantaiList)
                            <optgroup label="{{ $namaZona }}">
                                @foreach($pantaiList as $namaPantai => $coord)
                                    <option value="{{ $namaPantai }}">{{ $namaPantai }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach($daftarWilayah as $dw)
                            <option value="{{ $dw }}">{{ $dw }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="text-[11px] text-slate-400">Pemantau hanya dapat melihat dan menindaklanjuti laporan di wilayah pesisir yang ditugaskan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Organisasi / Komunitas (Opsional)</label>
                <input type="text" id="edit-organisasi" name="organisasi" placeholder="Contoh: DLH Surabaya / Komunitas Peduli Pesisir"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#004d6d] focus:ring focus:ring-[#004d6d]/10 text-xs sm:text-sm text-slate-800">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="delete-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative modal-anim">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>

        <h3 class="font-serif-title text-xl font-bold text-slate-900 mb-2">Hapus Akun Pengguna</h3>
        <p class="text-xs text-slate-600 mb-4">
            Apakah Anda yakin ingin menghapus akun pengguna <strong id="delete-user-name" class="text-slate-900"></strong> (<span id="delete-user-email" class="text-slate-500"></span>)?
        </p>
        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-[11px] mb-6">
            Peringatan: Seluruh data terkait pengguna ini akan dihapus secara permanen dari sistem.
        </div>

        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white transition-colors shadow-sm">
                    Ya, Hapus Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('add-modal')?.classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('add-modal')?.classList.add('hidden');
    }

    function toggleAddWilayahField(role) {
        const container = document.getElementById('add-wilayah-container');
        if (container) {
            if (role === 'pemantau') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    }

    function openEditModal(userData) {
        const form = document.getElementById('edit-form');
        form.action = '{{ url('/admin/pengguna') }}/' + userData.id;

        document.getElementById('edit-nama').value = userData.nama || '';
        document.getElementById('edit-email').value = userData.email || '';
        document.getElementById('edit-organisasi').value = userData.organisasi || '';

        const roleSelect = document.getElementById('edit-role-select');
        const roleHidden = document.getElementById('edit-role-hidden');
        roleSelect.value = userData.role || 'masyarakat';

        const warning = document.getElementById('edit-role-warning');
        if (userData.is_self) {
            roleSelect.setAttribute('disabled', 'disabled');
            if (roleHidden) {
                roleHidden.removeAttribute('disabled');
                roleHidden.value = userData.role || 'admin';
            }
            warning?.classList.remove('hidden');
        } else {
            roleSelect.removeAttribute('disabled');
            if (roleHidden) {
                roleHidden.setAttribute('disabled', 'disabled');
            }
            warning?.classList.add('hidden');
        }

        const wilayahSelect = document.getElementById('edit-wilayah-select');
        wilayahSelect.value = userData.wilayah_tugas || '';

        toggleEditWilayahField(userData.role);

        document.getElementById('edit-modal')?.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal')?.classList.add('hidden');
    }

    function toggleEditWilayahField(role) {
        const container = document.getElementById('edit-wilayah-container');
        if (container) {
            if (role === 'pemantau') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    }

    function openDeleteModal(id, nama, email) {
        const form = document.getElementById('delete-form');
        form.action = '{{ url('/admin/pengguna') }}/' + id;

        document.getElementById('delete-user-name').textContent = nama;
        document.getElementById('delete-user-email').textContent = email;

        document.getElementById('delete-modal')?.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal')?.classList.add('hidden');
    }
</script>
@endpush
@endsection
