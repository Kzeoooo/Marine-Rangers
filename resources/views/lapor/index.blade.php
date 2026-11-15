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
                    Pantau dan kelola seluruh laporan sampah pesisir dari seluruh wilayah kota Surabaya.
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
            @endif
        </div>
    </div>

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
                @if(request()->hasAny(['status', 'wilayah']))
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
                            <th class="py-3 px-4">ID / Wilayah</th>
                            <th class="py-3 px-4">Kategori Sampah</th>
                            @if(!auth()->user()->isMasyarakat())
                                <th class="py-3 px-4">Pelapor</th>
                            @endif
                            <th class="py-3 px-4">Waktu Laporan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($laporans as $lap)
                            <tr class="hover:bg-slate-50/80 transition-colors">
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

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4">
                                    <x-badge-status :status="$lap->status" size="sm" :showMeaning="false" />
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('laporan.show', $lap->id_laporan) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#004d6d] hover:text-[#00364d] bg-sky-50/60 hover:bg-sky-100/70 px-3 py-1.5 rounded-lg transition-colors">
                                        <span>Lihat Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
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
@endsection
