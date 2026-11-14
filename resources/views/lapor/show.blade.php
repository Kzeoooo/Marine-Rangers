@extends('layouts.app')

@section('title', 'Laporan #' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT) . ' - Marine Rangers')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 sm:py-12">
    <!-- Back to Map Link (Mockup 4) -->
    <div class="mb-6">
        <a href="{{ route('peta') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#004d6d] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Peta
        </a>
    </div>

    <!-- Heading (Mockup 4) -->
    <div class="mb-8">
        <h1 class="font-serif-title text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 mb-2">
            Laporan #{{ str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT) }} — {{ $laporan->wilayah }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500">
            Dilaporkan oleh <span class="font-semibold text-slate-700">{{ $laporan->user->nama ?? 'Relawan' }}</span> · 
            {{ $laporan->waktu_laporan ? $laporan->waktu_laporan->translatedFormat('d F Y, H.i') : 'Waktu tidak tercatat' }}
        </p>
    </div>

    <!-- Stepper Status Tracker (Mockup 4) -->
    @php
        $statusLevels = [
            'baru' => 1,
            'dipantau' => 2,
            'ditindaklanjuti' => 3,
            'selesai' => 4,
        ];
        $currentLevel = $statusLevels[$laporan->status] ?? 1;
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 mb-8">
        <div class="relative max-w-xl mx-auto">
            <!-- Connecting Line Background -->
            <div class="absolute top-1/2 left-6 right-6 -translate-y-1/2 h-1 bg-slate-100 -z-0"></div>
            <!-- Active Connecting Line -->
            <div 
                class="absolute top-1/2 left-6 -translate-y-1/2 h-1 bg-[#004d6d] transition-all duration-500 -z-0"
                style="width: {{ (($currentLevel - 1) / 3) * 100 }}%;"
            ></div>

            <div class="flex items-center justify-between relative z-10">
                <!-- Step 1: Baru -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm
                        {{ $currentLevel >= 1 ? 'bg-[#004d6d] text-white ring-4 ring-[#e2f0f5]' : 'bg-slate-200 text-slate-500' }}">
                        1
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $currentLevel >= 1 ? 'text-[#004d6d]' : 'text-slate-400' }}">
                        Baru
                    </span>
                </div>

                <!-- Step 2: Dipantau -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm
                        {{ $currentLevel >= 2 ? 'bg-[#004d6d] text-white ring-4 ring-[#e2f0f5]' : 'bg-slate-200 text-slate-500' }}">
                        2
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $currentLevel >= 2 ? 'text-[#004d6d]' : 'text-slate-400' }}">
                        Dipantau
                    </span>
                </div>

                <!-- Step 3: Ditindaklanjuti -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm
                        {{ $currentLevel >= 3 ? 'bg-[#004d6d] text-white ring-4 ring-[#e2f0f5]' : 'bg-slate-200 text-slate-500' }}">
                        3
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $currentLevel >= 3 ? 'text-[#004d6d]' : 'text-slate-400' }}">
                        Ditindaklanjuti
                    </span>
                </div>

                <!-- Step 4: Selesai -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm
                        {{ $currentLevel >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-500' }}">
                        4
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $currentLevel >= 4 ? 'text-emerald-700' : 'text-slate-400' }}">
                        Selesai
                    </span>
                </div>
            </div>
        </div>

        <!-- Role Action: Status Quick Switcher for Pemantau ONLY -->
        @auth
            @if(Auth::user()->isPemantau() && !$laporan->isSelesai())
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-medium">Aksi Tahapan Pemantau ({{ Auth::user()->wilayah_tugas }}):</span>
                    <div class="flex items-center gap-2">
                        @if($laporan->status === 'baru')
                            <form action="{{ route('laporan.update_status', $laporan->id_laporan) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="dipantau">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-sky-100 text-[#004d6d] hover:bg-sky-200 font-semibold transition-colors flex items-center gap-1.5">
                                    <span>→ Verifikasi Lapangan (Dipantau)</span>
                                </button>
                            </form>
                        @elseif($laporan->status === 'dipantau')
                            <form action="{{ route('laporan.update_status', $laporan->id_laporan) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="ditindaklanjuti">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 font-semibold transition-colors flex items-center gap-1.5">
                                    <span>→ Mulai Penanganan (Ditindaklanjuti)</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @elseif(Auth::user()->isAdmin())
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mode Pengawasan Admin (Read-Only) — Pembaruan status dikelola langsung oleh Pemantau Wilayah: <strong class="text-slate-700 ml-1">{{ $laporan->wilayah }}</strong>
                    </span>
                </div>
            @endif
        @endauth
    </div>

    <!-- Middle Two Cards (Mockup 4: FOTO SEBELUM & LOKASI) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Card 1: FOTO SEBELUM (Mockup 4) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-4">
                    FOTO SEBELUM
                </span>

                <div class="w-full h-48 sm:h-56 rounded-xl bg-slate-100 overflow-hidden relative flex items-center justify-center border border-slate-100">
                    @if($laporan->foto && Storage::disk('public')->exists($laporan->foto))
                        <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto Sebelum" class="w-full h-full object-cover">
                    @else
                        <!-- Mockup Clean Placeholder -->
                        <div class="w-full h-full bg-[#d6e9ea] flex flex-col items-center justify-center text-slate-500 p-4 text-center">
                            <svg class="w-10 h-10 text-[#004d6d]/40 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs font-semibold text-[#004d6d]">{{ $laporan->kategori->nama_kategori ?? 'Sampah' }}</span>
                            <span class="text-[11px] text-slate-500">{{ $laporan->wilayah }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Caption (Mockup 4) -->
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600">
                <span class="font-semibold text-slate-800">{{ $laporan->kategori->nama_kategori ?? 'Sampah' }}</span> · 
                {{ $laporan->wilayah }}, {{ $laporan->deskripsi ?? 'Area bibir pantai' }}
            </div>
        </div>

        <!-- Card 2: LOKASI (Mockup 4) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-4">
                    LOKASI
                </span>

                <!-- Interactive Mini Map Container -->
                <div id="map-detail" class="w-full h-48 sm:h-56 rounded-xl border border-slate-100 z-0 overflow-hidden"></div>
            </div>

            <!-- Coordinates String (Mockup 4) -->
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                <span>Koordinat Titik:</span>
                <span class="font-mono font-semibold text-slate-800">{{ (float) $laporan->latitude }}, {{ (float) $laporan->longitude }}</span>
            </div>
        </div>
    </div>

    <!-- Bottom Card: Konfirmasi Aksi Pembersihan (Mockup 4) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 mb-6">
            Konfirmasi Aksi Pembersihan
        </h3>

        @if($laporan->isSelesai())
            @php
                $pembersihanData = $laporan->pembersihan->first();
            @endphp
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-6 space-y-4">
                <div class="flex items-center gap-3 text-emerald-800 font-bold text-sm">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                        ✓
                    </div>
                    <span>Laporan ini telah selesai dibersihkan!</span>
                </div>

                @if($pembersihanData)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-xs text-slate-700">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Waktu Eksekusi:</span>
                            <span class="font-semibold">{{ $pembersihanData->tanggal ? $pembersihanData->tanggal->format('d F Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Penanggung Jawab:</span>
                            <span class="font-semibold">{{ $pembersihanData->user->nama ?? 'Tim Komunitas Lingkungan' }}</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-600 bg-white p-3 rounded-lg border border-emerald-100">
                        <span class="font-semibold text-slate-700 block mb-1">Catatan Hasil:</span>
                        {{ $pembersihanData->deskripsi_hasil }}
                    </div>
                @endif
            </div>

        @elseif(Auth::check() && Auth::user()->isPemantau())
            <!-- Form Aksi Pembersihan untuk Pemantau (Mockup 4) -->
            <form action="{{ route('laporan.tindaklanjut', $laporan->id_laporan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Unggah Foto Bukti Sesudah Dibersihkan -->
                <div>
                    <input type="file" id="foto_hasil" name="foto_hasil" accept="image/*" class="hidden" onchange="previewCleanImage(this)">

                    <div 
                        onclick="document.getElementById('foto_hasil').click()"
                        class="border-2 border-dashed border-slate-200 hover:border-[#004d6d] rounded-2xl p-8 text-center cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-50 group"
                    >
                        <div id="cleanUploadPlaceholder" class="space-y-2">
                            <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:text-[#004d6d] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-slate-600 group-hover:text-[#004d6d]">
                                Unggah foto bukti sesudah dibersihkan
                            </p>
                        </div>

                        <div id="cleanPreviewContainer" class="hidden">
                            <img id="cleanImagePreview" src="" alt="Bukti Sesudah" class="max-h-52 mx-auto rounded-xl object-contain border border-slate-200">
                            <p class="mt-2 text-xs text-[#004d6d] font-semibold underline">Ganti Foto Bukti</p>
                        </div>
                    </div>
                </div>

                <!-- Catatan Kegiatan -->
                <div>
                    <textarea 
                        name="deskripsi_hasil" 
                        rows="3" 
                        placeholder="Catatan kegiatan (opsional): jumlah relawan, volume sampah terangkut, dsb." 
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                    ></textarea>
                </div>

                <!-- Tombol Tandai Selesai (Mockup 4) -->
                <div>
                    <button type="submit" class="btn-primary px-8 py-3 rounded-xl font-semibold text-sm shadow-sm hover:shadow transition-all">
                        Tandai Selesai ✓
                    </button>
                </div>
            </form>

        @elseif(Auth::check() && Auth::user()->isAdmin())
            <!-- View untuk Administrator (Read-Only / Supervisor) -->
            <div class="rounded-2xl bg-indigo-50/50 border border-indigo-100 p-6 sm:p-7 text-xs text-slate-600 space-y-3">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-sm">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Peninjauan Administrator</span>
                </div>
                <p>
                    Laporan ini saat ini berstatus <strong class="text-slate-800 uppercase tracking-wide">{{ $laporan->status }}</strong>. Verifikasi lapangan, penanganan sampah, dan dokumentasi hasil pembersihan dilakukan secara mandiri oleh <strong>Pemantau Wilayah {{ $laporan->wilayah }}</strong>.
                </p>
                <div class="text-[11px] text-slate-400">
                    Sebagai Administrator, Anda dapat memantau performa seluruh pesisir melalui <a href="{{ route('admin') }}" class="text-indigo-600 font-semibold hover:underline">Panel Kontrol</a> atau <a href="{{ route('peta') }}" class="text-indigo-600 font-semibold hover:underline">Peta Spasial</a>.
                </div>
            </div>

        @else
            <!-- View untuk Masyarakat / Pengunjung -->
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-6 text-xs text-slate-600 space-y-2">
                <p class="font-semibold text-slate-800">Status Tindak Lanjut:</p>
                <p>
                    Laporan sedang dalam penanganan komunitas lingkungan dan pihak terkait pesisir Surabaya. 
                    Anda akan melihat dokumentasi hasil pembersihan begitu aksi selesai dilakukan.
                </p>
                <div class="pt-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-100 text-[#004d6d]">
                        Status Saat Ini: {{ ucfirst($laporan->status) }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Leaflet Mini Map (Mockup 4)
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $laporan->latitude ?? -7.2275 }};
        const lng = {{ $laporan->longitude ?? 112.7985 }};

        const map = L.map('map-detail', {
            center: [lat, lng],
            zoom: 15,
            zoomControl: false,
            attributionControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        // Custom red marker pin matching mockup
        const redIcon = L.divIcon({
            className: 'custom-pin',
            html: '<div style="background-color: #d93829; width: 14px; height: 14px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        L.marker([lat, lng], { icon: redIcon }).addTo(map);
    });

    function previewCleanImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('cleanImagePreview').src = e.target.result;
                document.getElementById('cleanUploadPlaceholder').classList.add('hidden');
                document.getElementById('cleanPreviewContainer').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
