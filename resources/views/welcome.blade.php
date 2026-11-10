@extends('layouts.app')

@section('title', 'Marine Rangers - Jaga Laut Bersama')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-16">
    <!-- Hero Section (Mockup 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center mb-20 lg:mb-28">
        <!-- Hero Left Column (Mockup 2) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- SDG 14 Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#e2f0f5] text-[#004d6d] text-xs font-semibold tracking-wide">
                <span>Mendukung SDG 14 — Ekosistem Lautan</span>
            </div>

            <!-- Main Heading (Serif font) -->
            <h1 class="font-serif-title text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 leading-[1.15]">
                Satu laporan darimu,<br>
                satu langkah lebih<br>
                dekat laut yang<br>
                bersih.
            </h1>

            <!-- Subheading Text -->
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-xl">
                Laporkan sampah di pantai atau laut dalam hitungan detik. Foto, titik lokasi, dan kategori — selebihnya biar Marine Rangers yang memetakan untuk aksi bersih-bersih yang tepat sasaran.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
                @auth
                    @if(Auth::user()->isMasyarakat())
                        <a href="{{ route('lapor') }}" class="btn-primary px-7 py-3.5 rounded-xl font-semibold text-sm shadow-sm flex items-center gap-2">
                            <span>Lapor Sampah Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('peta') }}" class="btn-primary px-7 py-3.5 rounded-xl font-semibold text-sm shadow-sm flex items-center gap-2">
                            <span>Pantau Peta Sebaran</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-primary px-7 py-3.5 rounded-xl font-semibold text-sm shadow-sm flex items-center gap-2">
                        <span>Lapor Sampah Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @endauth

                <a href="{{ route('peta') }}" class="btn-outline-primary px-7 py-3.5 rounded-xl font-semibold text-sm">
                    Lihat Peta Sebaran
                </a>
            </div>
        </div>

        <!-- Hero Right Column: Card Laporan Terbaru (Mockup 2) -->
        <div class="lg:col-span-5 flex justify-center lg:justify-end">
            <div class="w-full max-w-md bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-7">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        LAPORAN TERBARU
                    </span>
                    <a href="{{ route('peta') }}" class="text-xs font-semibold text-[#004d6d] hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($laporanTerbaru as $index => $item)
                        @php
                            $bgColors = ['bg-[#e2f0f5]', 'bg-[#ffeedb]', 'bg-[#eae8fe]', 'bg-[#e0f2fe]'];
                            $colorIndex = $index % count($bgColors);
                            $timeDiff = $item->waktu_laporan ? $item->waktu_laporan->diffForHumans() : 'Baru saja';
                        @endphp
                        <a href="{{ url('/laporan/' . $item->id_laporan) }}" class="flex items-center gap-4 p-2 rounded-xl hover:bg-slate-50 transition-colors group">
                            <!-- Colored Thumbnail box from mockup -->
                            <div class="w-12 h-12 rounded-xl {{ $bgColors[$colorIndex] }} flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6 text-[#004d6d]/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate group-hover:text-[#004d6d] transition-colors">
                                    {{ $item->wilayah }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $item->kategori->nama_kategori ?? 'Sampah Umum' }} · {{ $timeDiff }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 group-hover:text-[#004d6d] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @empty
                        <!-- Default Mockup items fallback if empty -->
                        <div class="flex items-center gap-4 p-2">
                            <div class="w-12 h-12 rounded-xl bg-[#e2f0f5] flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-[#004d6d]">PK</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Pantai Kenjeran</h4>
                                <p class="text-xs text-slate-500">Plastik · 2 jam lalu</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-2">
                            <div class="w-12 h-12 rounded-xl bg-[#ffeedb] flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-amber-700">KC</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Pantai Kedung Cowek</h4>
                                <p class="text-xs text-slate-500">Jaring · 5 jam lalu</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-2">
                            <div class="w-12 h-12 rounded-xl bg-[#eae8fe] flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-indigo-700">RK</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Pantai Ria Kenjeran</h4>
                                <p class="text-xs text-slate-500">Organik · 1 hari lalu</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: 3 Feature Cards (Mockup 2) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 hover:border-slate-200 transition-all">
            <div class="w-11 h-11 rounded-xl bg-[#004d6d] text-white flex items-center justify-center mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Lapor dalam &lt; 1 menit</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Foto, lokasi GPS otomatis, kategori, selesai.
            </p>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 hover:border-slate-200 transition-all">
            <div class="w-11 h-11 rounded-xl bg-[#0284c7] text-white flex items-center justify-center mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Peta Real-time</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Pantau sebaran sampah begitu laporan masuk.
            </p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 hover:border-slate-200 transition-all">
            <div class="w-11 h-11 rounded-xl bg-[#002b40] text-white flex items-center justify-center mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Berbasis Data</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Aksi bersih pantai diarahkan oleh data, bukan asumsi.
            </p>
        </div>
    </div>
</div>
@endsection
