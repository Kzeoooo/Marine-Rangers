@extends('layouts.app')

@section('title', 'Peta Sebaran Sampah - Marine Rangers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

    @if($isPemantauWilayah)
        <!-- Banner Mode Pemantau Khusus Wilayah -->
        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#e2f0f5] to-emerald-50 border border-[#004d6d]/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-[#004d6d] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm sm:text-base font-bold text-[#004d6d]">Area Penugasan: {{ $wilayahTugasPemantau }}</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#004d6d] text-white uppercase tracking-wider">
                            Pemantau Aktif
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Peta spasial dan statistik operasional difokuskan khusus untuk area Anda agar aksi penanganan sampah pesisir lebih cepat dan tertarget.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[#004d6d] bg-white px-3.5 py-2 rounded-xl border border-slate-200 shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Fokus Wilayah Terdekat</span>
            </div>
        </div>
    @endif

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

                <form id="filter-peta-form" action="{{ route('peta') }}" method="GET" class="space-y-6">
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

                    <!-- WILAYAH (Mockup 5 & Seluruh Indonesia) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                WILAYAH
                            </span>
                            @if(!$isPemantauWilayah)
                                <span class="text-[10px] text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded font-medium">Nusantara</span>
                            @endif
                        </div>
                        @if($isPemantauWilayah)
                            <div class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-700 flex items-center justify-between">
                                <span class="font-medium text-slate-800">{{ $wilayahTugasPemantau }}</span>
                                <span class="text-[10px] uppercase font-bold text-[#004d6d] bg-[#e2f0f5] px-2 py-0.5 rounded">Terkunci</span>
                            </div>
                            <input type="hidden" name="wilayah" value="{{ $wilayahTugasPemantau }}">
                        @else
                            <select 
                                name="wilayah" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer"
                            >
                                <option value="semua" {{ $wilayahFilter === 'semua' ? 'selected' : '' }}>Semua Pesisir Nusantara</option>
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
                                    @foreach($daftarWilayah as $wil)
                                        <option value="{{ $wil }}" {{ $wilayahFilter === $wil ? 'selected' : '' }}>
                                            {{ $wil }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>

                    <!-- TINGKAT PRIORITAS -->
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            TINGKAT PRIORITAS
                        </span>
                        <select 
                            name="prioritas" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] bg-white cursor-pointer"
                        >
                            <option value="">Semua Tingkat Prioritas</option>
                            <option value="tinggi" {{ request('prioritas') === 'tinggi' ? 'selected' : '' }}>🔴 Prioritas Tinggi</option>
                            <option value="sedang" {{ request('prioritas') === 'sedang' ? 'selected' : '' }}>🟡 Prioritas Sedang</option>
                            <option value="rendah" {{ request('prioritas') === 'rendah' ? 'selected' : '' }}>🟢 Prioritas Rendah</option>
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

                <!-- Live Counter Badge & API Indicator -->
                <div id="map-counter-pill" class="absolute top-7 right-7 z-20 bg-white/95 backdrop-blur-sm rounded-xl px-3.5 py-1.5 border border-slate-200 shadow-sm text-xs font-semibold text-[#004d6d] flex items-center gap-2">
                    <span id="counter-dot" class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span id="counter-text">{{ count($titikLaporan) }} titik terpetakan</span>
                    <a href="{{ url('/api/laporan') }}" target="_blank" title="Buka Endpoint JSON /api/laporan" class="text-[10px] bg-sky-50 hover:bg-sky-100 text-[#004d6d] px-2 py-0.5 rounded font-mono transition-colors">
                        /api/laporan
                    </a>
                </div>

                <!-- Map Legend Overlay (Mockup 5) -->
                <div class="absolute bottom-8 left-8 z-20 bg-white/95 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-slate-200 shadow-sm flex flex-wrap items-center gap-4 text-xs font-medium text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#d93829]"></span>
                        <span>Prioritas Tinggi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#e07a2c]"></span>
                        <span>Prioritas Sedang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span>
                        <span>Prioritas Rendah</span>
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
                TOTAL LAPORAN {{ $isPemantauWilayah ? '(' . strtoupper($wilayahTugasPemantau) . ')' : '' }}
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
                {{ $isPemantauWilayah ? 'STATUS WILAYAH ANDA' : 'WILAYAH PRIORITAS' }}
            </span>
            <div class="font-serif-title text-2xl sm:text-3xl font-bold text-slate-900 mb-1 truncate">
                {{ $namaWilayahPrioritas }}
            </div>
            <div class="text-xs text-slate-500">
                {{ $jumlahLaporanPrioritas }} laporan aktif belum selesai
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
                dari seluruh laporan {{ $isPemantauWilayah ? 'wilayah ini' : 'tercatat' }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultCenter = @json($defaultCenter);
        const defaultZoom = {{ $defaultZoom }};

        // Center around beach & coast
        const map = L.map('main-map', {
            center: defaultCenter,
            zoom: defaultZoom,
            zoomControl: true,
        });

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(map);

        // Marker Layer Group
        const markerGroup = L.layerGroup().addTo(map);

        function renderMarkers(items) {
            markerGroup.clearLayers();
            const bounds = [];

            items.forEach(item => {
                const markerColor = item.marker ? item.marker.color : (item.color || '#e07a2c');
                const markerIcon = L.divIcon({
                    className: 'map-custom-marker',
                    html: `
                        <div style="
                            background-color: ${markerColor};
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

                const lat = item.latitude !== undefined ? Number(item.latitude) : Number(item.lat);
                const lng = item.longitude !== undefined ? Number(item.longitude) : Number(item.lng);
                const kategoriNama = item.kategori ? (item.kategori.nama || item.kategori) : 'Sampah';
                const prioritas = item.prioritas || 'sedang';
                const status = (item.status || 'baru').toUpperCase();
                const waktu = item.waktu_lalu || item.waktu || '-';
                const urlDetail = item.url_detail || item.url || '#';

                const marker = L.marker([lat, lng], { icon: markerIcon });

                const priorityBadgeHtml = prioritas === 'tinggi' 
                    ? '<span style="background-color: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700; border: 1px solid #fca5a5;">🔴 Tinggi</span>'
                    : (prioritas === 'rendah'
                        ? '<span style="background-color: #d1fae5; color: #047857; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700; border: 1px solid #6ee7b7;">🟢 Rendah</span>'
                        : '<span style="background-color: #fef3c7; color: #b45309; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700; border: 1px solid #fcd34d;">🟡 Sedang</span>');

                const popupContent = `
                    <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px; min-width: 200px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; gap: 6px;">
                            <span style="font-size: 10px; font-weight: 700; color: #004d6d; text-transform: uppercase;">
                                ${kategoriNama}
                            </span>
                            ${priorityBadgeHtml}
                        </div>
                        <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 3px;">
                            ${item.wilayah}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                            Status: <b style="color: #004d6d">${status}</b> · ${waktu}
                        </div>
                        <a href="${urlDetail}" style="
                            display: block;
                            text-align: center;
                            background-color: #004d6d;
                            color: #ffffff;
                            padding: 6px 10px;
                            border-radius: 8px;
                            font-size: 11px;
                            font-weight: 600;
                            text-decoration: none;
                        ">
                            Lihat Detail Laporan →
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markerGroup.addLayer(marker);
                bounds.push([lat, lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
            }

            const counter = document.getElementById('counter-text');
            if (counter) {
                counter.textContent = items.length + ' titik terpetakan';
            }
        }

        // Render initial markers
        const initialMarkers = @json($titikLaporan);
        renderMarkers(initialMarkers);

        // Intercept Filter Form for Live /api/laporan AJAX updates
        const filterForm = document.getElementById('filter-peta-form');
        if (filterForm) {
            filterForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const dot = document.getElementById('counter-dot');
                if (dot) dot.className = 'w-2 h-2 rounded-full bg-amber-500 animate-ping';

                const formData = new FormData(filterForm);
                const params = new URLSearchParams();

                // Process checkboxes and inputs
                for (const [key, value] of formData.entries()) {
                    if (key.endsWith('[]')) {
                        params.append(key.replace('[]', ''), value);
                    } else {
                        params.set(key, value);
                    }
                }

                fetch('{{ url('/api/laporan') }}?' + params.toString())
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && Array.isArray(res.data)) {
                            renderMarkers(res.data);
                            window.history.pushState(null, '', '{{ route('peta') }}?' + params.toString());
                        }
                    })
                    .catch(err => {
                        console.error('Gagal mengambil titik laporan live:', err);
                        filterForm.submit(); // Fallback submit
                    })
                    .finally(() => {
                        if (dot) dot.className = 'w-2 h-2 rounded-full bg-emerald-500';
                    });
            });
        }
    });
</script>
@endpush
