@extends('layouts.app')

@section('title', 'Edit Laporan #' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT) . ' - Marine Rangers')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10 sm:py-14">
    <!-- Breadcrumb / Back Link -->
    <div class="mb-6">
        <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#004d6d] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Laporan Saya
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold mb-2">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Perbarui Data Laporan #{{ str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900 mb-2">
            Edit Laporan Insiden
        </h1>
        <p class="text-sm text-slate-500">
            Anda dapat menyesuaikan foto bukti, lokasi, kategori sampah, atau deskripsi sebelum laporan ditindaklanjuti.
        </p>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-10">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <div class="font-semibold mb-1">Terdapat kesalahan pada input laporan:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('laporan.update', $laporan->id_laporan) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="editReportForm">
            @csrf
            @method('PUT')

            <!-- 1. Foto Bukti -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Foto Bukti
                    <span class="text-slate-400 font-normal lowercase">(opsional, biarkan jika tidak ingin mengganti)</span>
                </label>
                
                <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="previewImage(this)">

                <div 
                    id="dropzone" 
                    onclick="document.getElementById('foto').click()" 
                    class="border-2 border-dashed border-slate-200 hover:border-[#004d6d] rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-50 group relative"
                >
                    <!-- Current / Preview Area -->
                    <div id="previewContainer" class="relative">
                        @if($laporan->foto)
                            <img id="imagePreview" src="{{ asset('storage/' . $laporan->foto) }}" alt="Bukti Foto" class="max-h-64 mx-auto rounded-xl object-contain shadow-sm border border-slate-200">
                        @else
                            <img id="imagePreview" src="" alt="Bukti Foto" class="max-h-64 mx-auto rounded-xl object-contain shadow-sm border border-slate-200 hidden">
                        @endif
                        <div class="mt-3 text-xs text-slate-500 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-[#004d6d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-[#004d6d] font-semibold underline">Ketuk untuk mengganti foto</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Lokasi -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Lokasi / Wilayah <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="detectGPS()" class="text-xs text-[#004d6d] font-semibold hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span id="gpsStatusText">Gunakan GPS Saya</span>
                    </button>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>

                    <input 
                        type="text" 
                        id="displayLokasi" 
                        name="wilayah" 
                        list="daftar-wilayah-datalist"
                        value="{{ old('wilayah', $laporan->wilayah) }}" 
                        required
                        placeholder="Pilih atau ketik wilayah pesisir di Indonesia..." 
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all bg-white"
                    >
                    <datalist id="daftar-wilayah-datalist">
                        @foreach(\App\Helpers\WilayahHelper::allNames() as $namaPantai)
                            <option value="{{ $namaPantai }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Hidden Latitude and Longitude -->
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $laporan->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $laporan->longitude) }}">
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Koordinat aktif: <span id="coordDisplay">{{ $laporan->latitude }}, {{ $laporan->longitude }}</span></span>
                    </span>
                    <span class="text-[10px] text-slate-400 italic">Mencakup pesisir seluruh Nusantara</span>
                </p>
            </div>

            <!-- 3. Kategori Sampah -->
            <div>
                <label for="id_kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Kategori Sampah <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select 
                        name="id_kategori" 
                        id="id_kategori" 
                        required 
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all appearance-none bg-white cursor-pointer"
                    >
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori', $laporan->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 4. Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Deskripsi Insiden (opsional)
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Contoh: tumpukan sampah plastik di dekat tangga masuk pantai" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                >{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center justify-between gap-4 border-t border-slate-100">
                <a href="{{ route('laporan.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs sm:text-sm font-semibold text-slate-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="btn-primary py-3 px-6 rounded-xl font-semibold text-xs sm:text-sm shadow-sm hover:shadow-md transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreview');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function detectGPS() {
        const statusSpan = document.getElementById('gpsStatusText');
        if (navigator.geolocation) {
            statusSpan.innerText = 'Mencari GPS...';
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(7);
                    const lng = position.coords.longitude.toFixed(7);
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('coordDisplay').innerText = lat + ', ' + lng;
                    document.getElementById('displayLokasi').value = 'Koordinat GPS — Wilayah Pesisir (' + lat + ', ' + lng + ')';
                    statusSpan.innerText = 'GPS Terdeteksi ✓';
                },
                function(error) {
                    statusSpan.innerText = 'GPS Gagal Didapat';
                }
            );
        }
    }

    // Auto-update coordinates when picking a preset region from datalist
    const wilayahCoordinates = @json(collect(\App\Helpers\WilayahHelper::ZONA_WILAYAH)->collapse());
    const lokasiInput = document.getElementById('displayLokasi');
    if (lokasiInput) {
        lokasiInput.addEventListener('change', function() {
            const val = this.value.trim();
            if (wilayahCoordinates[val]) {
                const coord = wilayahCoordinates[val];
                document.getElementById('latitude').value = coord.lat;
                document.getElementById('longitude').value = coord.lng;
                document.getElementById('coordDisplay').innerText = coord.lat + ', ' + coord.lng;
            }
        });
    }
</script>
@endpush
