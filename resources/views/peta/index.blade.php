@extends('layouts.app')

@section('title', 'Peta Sebaran Sampah - Marine Rangers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Top Row: Filter Sidebar & Map (Mockup 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <!-- Left Filter Card (Mockup 5) -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sticky top-24">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Filter</h2>
                    <a href="{{ route('peta') }}" class="text-xs font-semibold text-slate-400 hover:text-[#004d6d]">
                        Reset
                    </a>
                </div>

                <form action="{{ route('peta') }}" method="GET" class="space-y-6">
                    <!-- JENIS SAMPAH (Mockup 5) -->
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3">
                            JENIS SAMPAH
                        </span>
                        <div class="space-y-2.5">
                            @foreach($kategoris as $kat)
                                @php
                                    $isChecked = empty($selectedKategori) || in_array($kat->id_kategori, (array)$selectedKategori);
                                @endphp
                                <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:text-[#004d6d]">
                                    <input 
                                        type="checkbox" 
                                        name="kategori[]" 
                                        value="{{ $kat->id_kategori }}" 
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-[#004d6d] focus:ring-[#004d6d] border-slate-300"
                                    >
                                    <span>{{ $kat->nama_kategori }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- WILAYAH (Mockup 5) -->
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            WILAYAH
                        </span>
                        <select 
                            name="wilayah" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer"
                        >
                            <option value="semua" {{ $wilayahFilter === 'semua' ? 'selected' : '' }}>Surabaya, Jawa Timur (Semua)</option>
                            @foreach($daftarWilayah as $wil)
                                <option value="{{ $wil }}" {{ $wilayahFilter === $wil ? 'selected' : '' }}>
                                    {{ $wil }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PERIODE (Mockup 5) -->
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            PERIODE
                        </span>
                        <select 
                            name="periode" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer"
                        >
                            <option value="7_hari" {{ $periodeFilter === '7_hari' ? 'selected' : '' }}>7 hari terakhir</option>
                            <option value="30_hari" {{ $periodeFilter === '30_hari' ? 'selected' : '' }}>30 hari terakhir</option>
                            <option value="semua" {{ $periodeFilter === 'semua' ? 'selected' : '' }}>Semua waktu</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary w-full py-2.5 rounded-xl text-xs font-semibold shadow-sm hover:shadow transition-all">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Map Area (Mockup 5) -->
        <div class="lg:col-span-9">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 relative overflow-hidden">
                <!-- Leaflet Map Container -->
                <div id="main-map" class="w-full h-[480px] sm:h-[540px] rounded-xl z-10"></div>

                <!-- Map Legend Overlay (Mockup 5) -->
                <div class="absolute bottom-8 left-8 z-20 bg-white/95 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-slate-200 shadow-sm flex items-center gap-4 text-xs font-medium text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#d93829]"></span>
                        <span>Prioritas tinggi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#e07a2c]"></span>
                        <span>Sedang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#026670]"></span>
                        <span>Dipantau</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom 3 Stat Cards (Mockup 5) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stat Card 1: TOTAL LAPORAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                TOTAL LAPORAN
            </span>
            <div class="font-serif-title text-4xl sm:text-5xl font-bold text-slate-900 mb-1">
                {{ $totalLaporan }}
            </div>
            <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                <span>+{{ $laporanMingguIni }} minggu ini</span>
            </div>
        </div>

        <!-- Stat Card 2: WILAYAH PRIORITAS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                WILAYAH PRIORITAS
            </span>
            <div class="font-serif-title text-2xl sm:text-3xl font-bold text-slate-900 mb-1 truncate">
                {{ $namaWilayahPrioritas }}
            </div>
            <div class="text-xs text-slate-500">
                {{ $jumlahLaporanPrioritas }} laporan aktif
            </div>
        </div>

        <!-- Stat Card 3: SUDAH DITINDAKLANJUTI -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                SUDAH DITINDAKLANJUTI
            </span>
            <div class="font-serif-title text-4xl sm:text-5xl font-bold text-slate-900 mb-1">
                {{ $persentaseDitindaklanjuti }}%
            </div>
            <div class="text-xs text-slate-500">
                dari seluruh laporan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Center around Surabaya beach & coast
        const map = L.map('main-map', {
            center: [-7.2275, 112.7888],
            zoom: 12,
            zoomControl: true,
        });

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(map);

        const markersData = @json($titikLaporan);

        if (markersData.length > 0) {
            const bounds = [];

            markersData.forEach(item => {
                const markerIcon = L.divIcon({
                    className: 'map-custom-marker',
                    html: `
                        <div style="
                            background-color: ${item.color};
                            width: 14px;
                            height: 14px;
                            border-radius: 50%;
                            border: 3px solid #ffffff;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.35);
                            transition: transform 0.2s ease;
                            cursor: pointer;
                        "></div>
                    `,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                const marker = L.marker([item.lat, item.lng], { icon: markerIcon }).addTo(map);

                // Popup content with detail link
                const popupContent = `
                    <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px; min-width: 180px;">
                        <div style="font-size: 11px; font-weight: 700; color: #004d6d; text-transform: uppercase; margin-bottom: 2px;">
                            ${item.kategori}
                        </div>
                        <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                            ${item.wilayah}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                            Status: <b style="color: ${item.color}">${item.status.toUpperCase()}</b> · ${item.waktu}
                        </div>
                        <a href="${item.url}" style="
                            display: block;
                            text-align: center;
                            background-color: #004d6d;
                            color: #ffffff;
                            padding: 6px 10px;
                            border-radius: 6px;
                            font-size: 11px;
                            font-weight: 600;
                            text-decoration: none;
                        ">
                            Lihat Detail Laporan →
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                bounds.push([item.lat, item.lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
            }
        }
    });
</script>
@endpush
