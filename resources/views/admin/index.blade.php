@extends('layouts.app')

@section('title', 'Panel Administrator - Marine Rangers')

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
                Panel Kontrol Sistem
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola seluruh laporan insiden sampah, aktivitas operasional, dan akun pengguna di pesisir.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Kelola Akun Pengguna</span>
            </a>
            <a href="{{ route('peta') }}" class="btn-outline-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Lihat Peta Spasial</span>
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- TOTAL -->
        <div class="bg-gradient-to-br from-sky-50/70 via-white to-white rounded-2xl border border-sky-100 border-l-4 border-l-sky-600 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-sky-700 block mb-2">Total Laporan</span>
            <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-slate-900 tabular-nums">{{ number_format($totalLaporan) }}</div>
            <span class="text-xs text-slate-500 mt-1.5 block">Seluruh laporan tercatat</span>
        </div>

        <!-- BARU -->
        <div class="bg-gradient-to-br from-rose-50/70 via-white to-white rounded-2xl border border-rose-100 border-l-4 border-l-rose-500 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 block mb-2">Perlu Penanganan</span>
            <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-rose-600 tabular-nums">{{ number_format($laporanBaru) }}</div>
            <span class="text-xs text-slate-500 mt-1.5 block">Menunggu verifikasi</span>
        </div>

        <!-- DIPANTAU -->
        <div class="bg-gradient-to-br from-amber-50/70 via-white to-white rounded-2xl border border-amber-100 border-l-4 border-l-amber-500 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 block mb-2">Dalam Pemantauan</span>
            <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-amber-500 tabular-nums">{{ number_format($laporanDipantau ?? 0) }}</div>
            <span class="text-xs text-slate-500 mt-1.5 block">Dalam penanganan tim</span>
        </div>

        <!-- SELESAI -->
        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-white rounded-2xl border border-emerald-100 border-l-4 border-l-emerald-500 shadow-sm p-6 hover:shadow-md transition-all">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-2">Aksi Selesai</span>
            <div class="font-serif-title text-3xl sm:text-4xl font-extrabold text-emerald-600 tabular-nums">{{ number_format($laporanSelesai) }}</div>
            <span class="text-xs text-slate-500 mt-1.5 block">Aksi pembersihan tuntas</span>
        </div>
    </div>

    <!-- Tables Section: 2 Columns on LG -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main: Reports Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Daftar Laporan Terkini</h2>
                <span class="text-xs text-slate-400">{{ $laporans->total() }} Laporan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <tr>
                            <th class="py-3 px-3">ID / Wilayah</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Pelapor</th>
                            <th class="py-3 px-3">Prioritas</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laporans as $lap)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-900 block">#{{ str_pad($lap->id_laporan, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-slate-500 text-[11px]">{{ $lap->wilayah }}</span>
                                </td>
                                <td class="py-3 px-3 font-medium text-slate-700">
                                    {{ $lap->kategori->nama_kategori ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $lap->user->nama ?? 'Warga' }}
                                </td>
                                <td class="py-3 px-3">
                                    <x-badge-prioritas :prioritas="$lap->prioritas ?? 'sedang'" :skor="$lap->skor_prioritas" size="sm" />
                                </td>
                                <td class="py-3 px-3">
                                    @php
                                        $badgeClass = match($lap->status) {
                                            'baru' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'dipantau' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'ditindaklanjuti' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">
                                        {{ ucfirst($lap->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ url('/laporan/' . $lap->id_laporan) }}" class="text-[#004d6d] font-semibold hover:underline">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada laporan insiden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                {{ $laporans->links() }}
            </div>
        </div>

        <!-- Sidebar: Log Aktivitas Sistem -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Activity Log -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100">
                    Log Aktivitas Sistem
                </h3>

                <div class="space-y-3.5">
                    @forelse($logs as $log)
                        <div class="text-xs">
                            <div class="flex items-center justify-between text-slate-400 text-[10px] mb-0.5">
                                <span class="font-semibold text-slate-700">{{ $log->user->nama ?? 'Sistem' }}</span>
                                <span>{{ $log->waktu ? $log->waktu->diffForHumans() : '-' }}</span>
                            </div>
                            <p class="text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                {{ $log->aktivitas }}
                            </p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada log aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <!-- Users Overview -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">
                        Pengguna Terbaru
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                        Kelola Semua →
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($users as $u)
                        <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $u->nama }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $u->email }}</p>
                                    @if($u->isPemantau() && $u->wilayah_tugas)
                                        <span class="inline-block text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-medium mt-0.5">
                                            Wilayah: {{ $u->wilayah_tugas }}
                                        </span>
                                    @endif
                                </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold 
                                {{ $u->isAdmin() ? 'bg-indigo-50 text-indigo-700' : ($u->isPemantau() ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
