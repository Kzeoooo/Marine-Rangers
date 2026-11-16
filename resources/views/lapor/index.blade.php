@extends('layouts.app')

@section('title', 
    auth()->user()->isMasyarakat() ? 'Laporan Saya - Marine Rangers' : 
    (auth()->user()->isPemantau() ? 'Tugas Masuk - Marine Rangers' : 'Kelola Laporan - Marine Rangers')
)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            @if(auth()->user()->isMasyarakat())
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-sky-700 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>Warga Pesisir · Laporan Mandiri</span>
                </div>
                <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900">
                    Laporan Saya
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pantau riwayat dan progres tindak lanjut sampah pesisir yang telah Anda laporkan.
                </p>
            @elseif(auth()->user()->isPemantau())
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Pemantau Wilayah · {{ auth()->user()->wilayah_tugas ?? 'Pesisir Surabaya' }}</span>
                </div>
                <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900">
                    Daftar Tugas Masuk
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Daftar laporan aktif di wilayah tugas Anda yang siap diverifikasi, dipantau, dan ditindaklanjuti.
                </p>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Dinas Lingkungan Hidup Surabaya · Administrator</span>
                </div>
                <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900">
                    Kelola Seluruh Laporan
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pantau dan kelola seluruh insiden sampah pesisir, status verifikasi, dan tindak lanjut penanganan.
                </p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if(auth()->user()->isMasyarakat())
                <a href="{{ route('lapor') }}" class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm hover:shadow transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Lapor Sampah Baru</span>
                </a>
            @else
                <a href="{{ route('peta') }}" class="btn-outline-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span>Buka Peta Spasial</span>
                </a>
                <a href="{{ route('statistik') }}" class="btn-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Lihat Statistik</span>
                </a>
            @endif
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        <!-- 4 Stats Cards (Khusus Admin) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- TOTAL -->
            <div class="bg-gradient-to-br from-sky-50/70 via-white to-white rounded-2xl border border-sky-100 border-l-4 border-l-sky-600 shadow-sm p-6 hover:shadow-md transition-all">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700 block mb-2">Total Laporan</span>
                <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-slate-900 tabular-nums">{{ number_format($statCounts['total'] ?? $laporans->total()) }}</div>
                <span class="text-xs text-slate-500 mt-1.5 block">Seluruh laporan tercatat</span>
            </div>

            <!-- BARU -->
            <div class="bg-gradient-to-br from-rose-50/70 via-white to-white rounded-2xl border border-rose-100 border-l-4 border-l-rose-500 shadow-sm p-6 hover:shadow-md transition-all">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600 block mb-2">Perlu Penanganan</span>
                <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-rose-600 tabular-nums">{{ number_format($statCounts['baru'] ?? 0) }}</div>
                <span class="text-xs text-slate-500 mt-1.5 block">Menunggu verifikasi</span>
            </div>

            <!-- DIPANTAU -->
            <div class="bg-gradient-to-br from-amber-50/70 via-white to-white rounded-2xl border border-amber-100 border-l-4 border-l-amber-500 shadow-sm p-6 hover:shadow-md transition-all">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 block mb-2">Dalam Pemantauan</span>
                <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-amber-500 tabular-nums">{{ number_format($statCounts['dipantau'] ?? 0) }}</div>
                <span class="text-xs text-slate-500 mt-1.5 block">Dalam penanganan tim</span>
            </div>

            <!-- SELESAI -->
            <div class="bg-gradient-to-br from-emerald-50/70 via-white to-white rounded-2xl border border-emerald-100 border-l-4 border-l-emerald-500 shadow-sm p-6 hover:shadow-md transition-all">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-2">Aksi Selesai</span>
                <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-emerald-600 tabular-nums">{{ number_format($statCounts['selesai'] ?? 0) }}</div>
                <span class="text-xs text-slate-500 mt-1.5 block">Aksi pembersihan tuntas</span>
            </div>
        </div>
    @endif

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5 mb-8">
        <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Filter Status -->
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Laporan</label>
                    <select name="status" id="status" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer transition-all">
                        <option value="">Semua Status</option>
                        @foreach($availableStatuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>
                                {{ ucfirst($statusOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Prioritas -->
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label for="prioritas" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Prioritas</label>
                    <select name="prioritas" id="prioritas" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer transition-all">
                        <option value="">Semua Prioritas</option>
                        @foreach($availablePrioritas as $prioOption)
                            <option value="{{ $prioOption }}" {{ request('prioritas') === $prioOption ? 'selected' : '' }}>
                                {{ ucfirst($prioOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cari Wilayah (Khusus Admin) -->
                @if(auth()->user()->isAdmin())
                    <div class="w-full sm:w-auto flex-1 min-w-[200px]">
                        <label for="wilayah" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cari Wilayah</label>
                        <input 
                            type="text" 
                            name="wilayah" 
                            id="wilayah" 
                            value="{{ request('wilayah') }}" 
                            placeholder="Contoh: Pantai Kenjeran, Tambak Wedi..." 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                        >
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 self-end sm:self-auto pt-2 sm:pt-4">
                <button type="submit" class="btn-primary px-4 py-2 rounded-xl text-xs font-semibold shadow-sm hover:shadow transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>
                @if(request()->hasAny(['status', 'prioritas', 'wilayah']))
                    <a href="{{ route('laporan.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Content: Daftar Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">
                @if(auth()->user()->isMasyarakat())
                    Daftar Laporan Anda
                @elseif(auth()->user()->isPemantau())
                    Tugas di Wilayah {{ auth()->user()->wilayah_tugas ?? 'Tugas' }}
                @else
                    Seluruh Laporan Pesisir
                @endif
            </h2>
            <span class="text-xs font-medium text-slate-400">
                Total: {{ $laporans->total() }} Laporan
            </span>
        </div>

        @if($laporans->isEmpty())
            <div class="p-8 sm:p-12">
                @if(auth()->user()->isMasyarakat())
                    <x-empty-state 
                        title="Belum Ada Laporan Terkirim" 
                        description="Anda belum memiliki riwayat pelaporan insiden sampah pesisir. Ambil foto sampah di pantai terdekat dan laporkan sekarang." 
                        icon="net"
                        actionText="Lapor Sampah Sekarang" 
                        actionHref="{{ route('lapor') }}" 
                    />
                @elseif(auth()->user()->isPemantau())
                    <x-empty-state 
                        title="Tidak Ada Tugas Aktif" 
                        description="Saat ini belum ada laporan sampah dengan status dipantau atau ditindaklanjuti pada wilayah pengawasan Anda." 
                        icon="wave"
                        actionText="Lihat Peta Sebaran" 
                        actionHref="{{ route('peta') }}" 
                    />
                @else
                    <x-empty-state 
                        title="Tidak Ada Laporan Ditemukan" 
                        description="Tidak ada data laporan yang cocok dengan kriteria pencarian atau filter yang Anda terapkan." 
                        icon="alert-circle"
                        actionText="Reset Filter" 
                        actionHref="{{ route('laporan.index') }}" 
                    />
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 bg-slate-50/60 border-b border-slate-100">
                        <tr>
                            @if(auth()->user()->isAdmin())
                                <th class="py-3.5 px-4 font-semibold text-slate-500">Tanggal</th>
                                <th class="py-3.5 px-4 font-semibold text-slate-500">Pelapor</th>
                                <th class="py-3.5 px-4 font-semibold text-slate-500">Lokasi</th>
                                <th class="py-3.5 px-4 font-semibold text-slate-500">Kategori</th>
                                <th class="py-3.5 px-4 font-semibold text-slate-500">Status</th>
                                <th class="py-3.5 px-4 font-semibold text-slate-500 text-right">Aksi</th>
                            @else
                                <th class="py-3 px-4">ID / Wilayah</th>
                                <th class="py-3 px-4">Kategori Sampah</th>
                                @if(!auth()->user()->isMasyarakat())
                                    <th class="py-3 px-4">Pelapor</th>
                                @endif
                                <th class="py-3 px-4">Waktu Laporan</th>
                                <th class="py-3 px-4">Prioritas</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($laporans as $lap)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                @if(auth()->user()->isAdmin())
                                    <!-- Tanggal -->
                                    <td class="py-4 px-4 text-slate-700 font-medium whitespace-nowrap">
                                        {{ $lap->waktu_laporan ? $lap->waktu_laporan->translatedFormat('d M Y') : '-' }}
                                    </td>

                                    <!-- Pelapor -->
                                    <td class="py-4 px-4 text-slate-800 font-semibold whitespace-nowrap">
                                        {{ $lap->user->nama ?? 'Warga' }}
                                    </td>

                                    <!-- Lokasi -->
                                    <td class="py-4 px-4 text-slate-700">
                                        <div class="font-medium text-slate-900 whitespace-nowrap">{{ $lap->wilayah }}</div>
                                        @if($lap->deskripsi)
                                            <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $lap->deskripsi }}</div>
                                        @endif
                                    </td>

                                    <!-- Kategori -->
                                    <td class="py-4 px-4 text-slate-700 font-medium whitespace-nowrap">
                                        {{ $lap->kategori->nama_kategori ?? 'Sampah' }}
                                    </td>

                                    <!-- Status (Soft Rounded Pill Badge sesuai Mockup) -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @php
                                            $statusStyle = match($lap->status) {
                                                'baru' => 'bg-rose-50 text-rose-600 border border-rose-100',
                                                'dipantau' => 'bg-amber-50 text-amber-700 border border-amber-100',
                                                'ditindaklanjuti' => 'bg-sky-50 text-sky-700 border border-sky-100',
                                                'selesai' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                                default => 'bg-slate-50 text-slate-700 border border-slate-200',
                                            };
                                        @endphp
                                        <span class="inline-flex px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusStyle }}">
                                            {{ ucfirst($lap->status) }}
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-3 justify-end">
                                            <a href="{{ route('laporan.show', $lap->id_laporan) }}" class="text-xs font-semibold text-sky-600 hover:text-sky-800 hover:underline">
                                                Lihat
                                            </a>

                                            @if($lap->isBaru())
                                                <a href="{{ route('laporan.edit', $lap->id_laporan) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800 hover:underline">
                                                    Edit
                                                </a>

                                                <button type="button" 
                                                    onclick="openDeleteLaporanModal({{ $lap->id_laporan }}, '{{ addslashes($lap->wilayah) }}')"
                                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @else
                                    <!-- ID / Wilayah -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0">
                                                #{{ str_pad($lap->id_laporan, 3, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-900 block">{{ $lap->wilayah }}</span>
                                                @if($lap->deskripsi)
                                                    <p class="text-[11px] text-slate-500 line-clamp-1 max-w-xs">{{ $lap->deskripsi }}</p>
                                                @endif
                                                <span class="text-[11px] text-slate-400 font-mono">{{ number_format($lap->latitude, 4) }}, {{ number_format($lap->longitude, 4) }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="py-3.5 px-4 font-medium text-slate-700">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#004d6d]"></span>
                                            <span>{{ $lap->kategori->nama_kategori ?? 'Sampah Umum' }}</span>
                                        </div>
                                    </td>

                                    <!-- Pelapor (Admin / Pemantau) -->
                                    @if(!auth()->user()->isMasyarakat())
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-slate-800">{{ $lap->user->nama ?? 'Warga' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $lap->user->email ?? '-' }}</div>
                                        </td>
                                    @endif

                                    <!-- Waktu Laporan -->
                                    <td class="py-3.5 px-4 text-slate-500">
                                        <span class="block text-slate-700 font-medium">
                                            {{ $lap->waktu_laporan ? $lap->waktu_laporan->translatedFormat('d M Y') : '-' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $lap->waktu_laporan ? $lap->waktu_laporan->translatedFormat('H.i') . ' WIB' : '' }}
                                        </span>
                                    </td>

                                    <!-- Prioritas Badge -->
                                    <td class="py-3.5 px-4">
                                        <x-badge-prioritas :prioritas="$lap->prioritas ?? 'sedang'" :skor="$lap->skor_prioritas" size="sm" />
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-3.5 px-4">
                                        <x-badge-status :status="$lap->status" size="sm" :showMeaning="false" />
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <a href="{{ route('laporan.show', $lap->id_laporan) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#004d6d] hover:text-[#00364d] bg-sky-50/70 hover:bg-sky-100/80 px-2.5 py-1.5 rounded-lg transition-colors">
                                                <span>Detail</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>

                                            @if(auth()->user()->isMasyarakat() && $lap->id_user === auth()->id())
                                                @if($lap->isBaru())
                                                    <a href="{{ route('laporan.edit', $lap->id_laporan) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        <span>Edit</span>
                                                    </a>

                                                    <button type="button" 
                                                        onclick="openDeleteLaporanModal({{ $lap->id_laporan }}, '{{ addslashes($lap->wilayah) }}')"
                                                        class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-2 py-1.5 rounded-lg transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 bg-slate-100 px-2 py-1 rounded-md cursor-help" title="Laporan ini telah diverifikasi dan masuk proses pembersihan sehingga terkunci.">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                        <span>Terkunci</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($laporans->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
                    {{ $laporans->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus Laporan -->
<div id="delete-laporan-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative modal-anim">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>

        <h3 class="font-serif-title text-xl font-bold text-slate-900 mb-2">Hapus Laporan Insiden</h3>
        <p class="text-xs text-slate-600 mb-4">
            Apakah Anda yakin ingin membatalkan dan menghapus laporan <strong id="delete-laporan-id" class="text-slate-900"></strong> di <span id="delete-laporan-wilayah" class="text-slate-700 font-medium"></span>?
        </p>
        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-[11px] mb-6">
            Laporan hanya dapat dihapus sebelum diverifikasi atau ditindaklanjuti oleh tim pemantau pesisir. Tindakan ini tidak dapat dibatalkan.
        </div>

        <form id="delete-laporan-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteLaporanModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white transition-colors shadow-sm">
                    Ya, Hapus Laporan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteLaporanModal(id, wilayah) {
        const form = document.getElementById('delete-laporan-form');
        form.action = '{{ url('/laporan') }}/' + id;

        document.getElementById('delete-laporan-id').textContent = '#' + String(id).padStart(4, '0');
        document.getElementById('delete-laporan-wilayah').textContent = wilayah;

        document.getElementById('delete-laporan-modal')?.classList.remove('hidden');
    }

    function closeDeleteLaporanModal() {
        document.getElementById('delete-laporan-modal')?.classList.add('hidden');
    }
</script>
@endpush
@endsection
