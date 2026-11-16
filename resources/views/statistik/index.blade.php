@extends('layouts.app')

@section('title', 'Dashboard Statistik Pesisir - Marine Rangers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-100 text-[#004d6d]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#004d6d] animate-pulse"></span>
                    Sistem Analitik Spasial Real-Time
                </span>
                <span class="text-xs text-slate-400">· Mendukung SDG 14</span>
            </div>
            <h1 class="font-serif-title text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Dashboard Statistik & Pemantauan Pesisir
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Visualisasi tren insiden sampah, laju penanganan aksi bersih laut, dan peringkat pencemaran wilayah pesisir.
            </p>
        </div>

        <!-- Filter Periode & Wilayah Toolbar -->
        <form method="GET" action="{{ route('statistik') }}" id="filter-statistik-form" class="flex flex-wrap items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
            <!-- Filter Wilayah -->
            <select name="wilayah" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] cursor-pointer">
                <option value="semua" {{ $wilayahFilter === 'semua' ? 'selected' : '' }}>Semua Pesisir</option>
                @if(isset($zonaWilayah) && is_array($zonaWilayah))
                    @foreach($zonaWilayah as $namaZona => $pantaiList)
                        <optgroup label="{{ $namaZona }}">
                            @foreach($pantaiList as $namaPantai => $coord)
                                <option value="{{ $namaPantai }}" {{ $wilayahFilter === $namaPantai ? 'selected' : '' }}>
                                    {{ $namaPantai }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @else
                    @foreach($daftarWilayah as $dw)
                        <option value="{{ $dw }}" {{ $wilayahFilter === $dw ? 'selected' : '' }}>{{ $dw }}</option>
                    @endforeach
                @endif
            </select>

            <!-- Filter Periode Tab Buttons -->
            <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs">
                <a href="{{ route('statistik', ['periode' => '7_hari', 'wilayah' => $wilayahFilter]) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold transition-all {{ $periode === '7_hari' ? 'bg-white text-[#004d6d] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   7 Hari
                </a>
                <a href="{{ route('statistik', ['periode' => '30_hari', 'wilayah' => $wilayahFilter]) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold transition-all {{ $periode === '30_hari' ? 'bg-white text-[#004d6d] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   30 Hari
                </a>
                <a href="{{ route('statistik', ['periode' => 'semua', 'wilayah' => $wilayahFilter]) }}" 
                   class="px-3 py-1.5 rounded-lg font-semibold transition-all {{ $periode === 'semua' ? 'bg-white text-[#004d6d] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   Semua
                </a>
            </div>

            <!-- Tombol Refresh Live Data -->
            <button type="button" onclick="refreshLiveStatistik()" id="btn-refresh" title="Segarkan Data" class="p-2 text-slate-500 hover:text-[#004d6d] hover:bg-sky-50 rounded-xl transition-colors">
                <svg id="icon-refresh" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- 4 Kartu KPI Ringkasan Metrik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- KPI 1: Total Laporan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Laporan</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-[#004d6d] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-serif-title text-3xl font-bold text-slate-900" id="stat-total-laporan">{{ number_format($ringkasan['total_laporan']) }}</span>
                <span class="text-xs text-slate-500">insiden</span>
            </div>
            <div class="mt-3 flex items-center gap-2 text-[11px] text-slate-500 pt-3 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    {{ $ringkasan['laporan_baru'] }} Baru
                </span>
                <span>·</span>
                <span class="inline-flex items-center gap-1 font-semibold text-sky-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    {{ $ringkasan['laporan_proses'] }} Diproses
                </span>
            </div>
        </div>

        <!-- KPI 2: Tingkat Penyelesaian -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Penanganan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-serif-title text-3xl font-bold text-emerald-600" id="stat-persen-selesai">{{ $ringkasan['persentase_selesai'] }}%</span>
                <span class="text-xs text-slate-500">tuntas</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $ringkasan['persentase_selesai'] }}%"></div>
                </div>
                <div class="mt-1.5 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $ringkasan['laporan_selesai'] }} laporan selesai</span>
                    <span>Target > 80%</span>
                </div>
            </div>
        </div>

        <!-- KPI 3: Aktivitas 7 Hari Terakhir -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Aktivitas Minggu Ini</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-serif-title text-3xl font-bold text-slate-900" id="stat-minggu-ini">{{ $ringkasan['laporan_minggu_ini'] }}</span>
                <span class="text-xs text-slate-500">laporan baru</span>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] text-indigo-700 pt-3 border-t border-slate-100">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>Partisipasi warga & pemantau aktif</span>
            </div>
        </div>

        <!-- KPI 4: Titik Wilayah Paling Rawan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Wilayah Paling Rawan</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="truncate">
                <div class="font-serif-title text-lg font-bold text-slate-900 truncate" id="stat-wilayah-tertinggi" title="{{ $ringkasan['wilayah_tertinggi'] }}">
                    {{ $ringkasan['wilayah_tertinggi'] }}
                </div>
                <div class="text-xs text-rose-600 font-semibold mt-0.5">
                    {{ $ringkasan['jumlah_tertinggi'] }} insiden tercatat
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 pt-3 border-t border-slate-100">
                <span class="text-slate-400">Fokus patroli DLH</span>
                <a href="{{ route('peta', ['wilayah' => $ringkasan['wilayah_tertinggi']]) }}" class="text-[#004d6d] font-semibold hover:underline">
                    Lihat Peta →
                </a>
            </div>
        </div>
    </div>

    <!-- Grafik Baris 1: Tren Laporan Waktu ke Waktu & Komposisi Kategori -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Tren Laporan (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <h3 class="font-serif-title text-lg font-bold text-slate-900">Perkembangan Laporan dari Waktu ke Waktu</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tren volume timbunan sampah baru dibandingkan aksi pembersihan tuntas.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium">
                    <span class="inline-flex items-center gap-1.5 text-[#004d6d]">
                        <span class="w-3 h-3 rounded-full bg-[#004d6d]"></span> Total Insiden
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-600">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Tuntas Bersih
                    </span>
                </div>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="chart-tren-laporan"></canvas>
            </div>
        </div>

        <!-- Komposisi Kategori Sampah (1 Kolom) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="mb-4">
                <h3 class="font-serif-title text-lg font-bold text-slate-900">Komposisi Sampah Laut</h3>
                <p class="text-xs text-slate-400 mt-0.5">Proporsi jenis limbah yang mencemari garis pantai.</p>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="chart-kategori-sampah"></canvas>
            </div>
            <!-- List Kategori dengan Persentase -->
            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 max-h-40 overflow-y-auto pr-1">
                @forelse($per_kategori as $kat)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $kat['color'] }}"></span>
                            <span class="text-slate-700 font-medium truncate max-w-[140px]">{{ $kat['nama'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800">{{ $kat['total'] }}</span>
                            <span class="text-slate-400 text-[11px]">({{ $kat['persentase'] }}%)</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-2">Belum ada data kategori.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Grafik Baris 2: Peringkat Wilayah Pencemaran Tertinggi & Distribusi Prioritas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Peringkat Wilayah dengan Tingkat Pencemaran Tertinggi (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <h3 class="font-serif-title text-lg font-bold text-slate-900">Peringkat Wilayah Tingkat Pencemaran Tertinggi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Lokasi pesisir dengan konsentrasi akumulasi laporan insiden sampah terbanyak.</p>
                </div>
                <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg self-start sm:self-auto">
                    Top 10 Wilayah
                </span>
            </div>

            <!-- Tabel / Bar Leaderboard Wilayah -->
            <div class="space-y-4">
                @forelse($peringkat_wilayah as $idx => $wil)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold 
                                    {{ $idx === 0 ? 'bg-rose-100 text-rose-700 ring-2 ring-rose-200' : ($idx === 1 ? 'bg-amber-100 text-amber-700' : ($idx === 2 ? 'bg-sky-100 text-[#004d6d]' : 'bg-slate-200 text-slate-600')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="font-bold text-sm text-slate-900">{{ $wil['wilayah'] }}</span>
                                    <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">
                                        ({{ $wil['total'] }} laporan · {{ $wil['persentase_dari_total'] }}% dari total)
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $wil['persentase_selesai'] >= 70 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $wil['selesai'] }}/{{ $wil['total'] }} Selesai ({{ $wil['persentase_selesai'] }}%)
                                </span>
                            </div>
                        </div>

                        <!-- Stacked Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden flex">
                            <div class="bg-emerald-500 h-2 transition-all duration-500" style="width: {{ $wil['persentase_selesai'] }}%" title="Selesai: {{ $wil['selesai'] }}"></div>
                            <div class="bg-amber-400 h-2 transition-all duration-500" style="width: {{ 100 - $wil['persentase_selesai'] }}%" title="Belum Selesai: {{ $wil['belum_selesai'] }}"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Belum ada data laporan wilayah pesisir untuk periode ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Distribusi Tingkat Prioritas & Status Laporan (1 Kolom) -->
        <div class="space-y-6">
            <!-- Prioritas Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <div class="mb-4">
                    <h3 class="font-serif-title text-lg font-bold text-slate-900">Urgensi Prioritas</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Klasifikasi tingkat keparahan dampak ekologis.</p>
                </div>

                <div class="space-y-3">
                    <!-- Prioritas Tinggi -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-bold text-rose-900">Prioritas Tinggi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-serif-title text-lg font-bold text-rose-700">{{ $per_prioritas['tinggi'] }}</span>
                            <span class="text-[10px] text-rose-500">laporan</span>
                        </div>
                    </div>

                    <!-- Prioritas Sedang -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-bold text-amber-900">Prioritas Sedang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-serif-title text-lg font-bold text-amber-700">{{ $per_prioritas['sedang'] }}</span>
                            <span class="text-[10px] text-amber-500">laporan</span>
                        </div>
                    </div>

                    <!-- Prioritas Rendah -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-emerald-900">Prioritas Rendah</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-serif-title text-lg font-bold text-emerald-700">{{ $per_prioritas['rendah'] }}</span>
                            <span class="text-[10px] text-emerald-500">laporan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Endpoint API Helper Box -->
            <div class="bg-gradient-to-br from-[#004d6d] to-[#002b40] rounded-2xl p-6 text-white shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">REST API</span>
                    <span class="text-xs text-sky-200">Terbuka & Terintegrasi</span>
                </div>
                <h4 class="font-bold text-sm mb-1">Akses Data Melalui API JSON</h4>
                <p class="text-xs text-sky-100 leading-relaxed mb-4">
                    Gunakan endpoint resmi untuk integrasi sistem seluler atau riset lingkungan pesisir.
                </p>
                <div class="space-y-1.5 text-[11px] font-mono">
                    <a href="{{ url('/api/laporan') }}" target="_blank" class="block bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors truncate">
                        GET /api/laporan
                    </a>
                    <a href="{{ url('/api/statistik') }}" target="_blank" class="block bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors truncate">
                        GET /api/statistik
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Load Chart.js for Interactive Visual Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    let trenChart = null;
    let kategoriChart = null;

    // Initial Data from Backend
    const trenData = @json($tren);
    const kategoriData = @json($per_kategori);

    document.addEventListener('DOMContentLoaded', function () {
        initTrenChart(trenData.labels, trenData.total, trenData.selesai);
        initKategoriChart(kategoriData);
    });

    function initTrenChart(labels, totalSeries, selesaiSeries) {
        const ctx = document.getElementById('chart-tren-laporan').getContext('2d');
        if (trenChart) {
            trenChart.destroy();
        }

        const gradientTotal = ctx.createLinearGradient(0, 0, 0, 250);
        gradientTotal.addColorStop(0, 'rgba(0, 77, 109, 0.25)');
        gradientTotal.addColorStop(1, 'rgba(0, 77, 109, 0.0)');

        const gradientSelesai = ctx.createLinearGradient(0, 0, 0, 250);
        gradientSelesai.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        gradientSelesai.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        trenChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Insiden Dilaporkan',
                        data: totalSeries,
                        borderColor: '#004d6d',
                        backgroundColor: gradientTotal,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#004d6d',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Tuntas Ditangani (Selesai)',
                        data: selesaiSeries,
                        borderColor: '#10b981',
                        backgroundColor: gradientSelesai,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#002b40',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
    }

    function initKategoriChart(categories) {
        const ctx = document.getElementById('chart-kategori-sampah').getContext('2d');
        if (kategoriChart) {
            kategoriChart.destroy();
        }

        const labels = categories.map(c => c.nama);
        const data = categories.map(c => c.total);
        const colors = categories.map(c => c.color);

        kategoriChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#002b40',
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw;
                                const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Live Refresh Function fetching /api/statistik
    function refreshLiveStatistik() {
        const icon = document.getElementById('icon-refresh');
        icon.classList.add('animate-spin');

        const params = new URLSearchParams(new FormData(document.getElementById('filter-statistik-form')));

        fetch('{{ url('/api/statistik') }}?' + params.toString())
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update KPI numbers
                    document.getElementById('stat-total-laporan').textContent = Number(data.ringkasan.total_laporan).toLocaleString();
                    document.getElementById('stat-persen-selesai').textContent = data.ringkasan.persentase_selesai + '%';
                    document.getElementById('stat-minggu-ini').textContent = data.ringkasan.laporan_minggu_ini;
                    document.getElementById('stat-wilayah-tertinggi').textContent = data.ringkasan.wilayah_tertinggi;

                    // Update Charts
                    initTrenChart(data.tren.labels, data.tren.total, data.tren.selesai);
                    initKategoriChart(data.per_kategori);
                }
            })
            .catch(err => console.error('Gagal mengambil data statistik:', err))
            .finally(() => {
                setTimeout(() => {
                    icon.classList.remove('animate-spin');
                }, 400);
            });
    }
</script>
@endpush
