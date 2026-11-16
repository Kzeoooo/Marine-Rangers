@extends('layouts.app')

@section('title', 'Lapor Sampah Baru - Marine Rangers')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10 sm:py-14">
    <!-- Header (Mockup 3) -->
    <div class="text-center mb-8">
        <h1 class="font-serif-title text-3xl sm:text-4xl font-bold text-slate-900 mb-2">
            Lapor Sampah Baru
        </h1>
        <p class="text-sm text-slate-500">
            Isi 4 informasi singkat ini — biasanya kurang dari 1 menit.
        </p>
    </div>

    <!-- Main Form Card (Mockup 3) -->
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

        <form action="{{ route('lapor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="reportForm">
            @csrf

            <!-- 1. Foto Bukti (Mockup 3) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Foto Bukti <span class="text-rose-500">*</span>
                </label>
                
                <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" required onchange="previewImage(this)">

                <div 
                    id="dropzone" 
                    onclick="document.getElementById('foto').click()" 
                    class="border-2 border-dashed border-slate-200 hover:border-[#004d6d] rounded-2xl p-8 text-center cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-50 group relative"
                >
                    <div id="uploadPlaceholder" class="space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:text-[#004d6d] group-hover:bg-[#e2f0f5] transition-colors">
                            <!-- Camera Icon from Mockup -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-600 group-hover:text-[#004d6d] transition-colors">
                            Ketuk untuk ambil foto / unggah gambar
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Format JPG, PNG, atau WebP (Maks. 5MB)
                        </p>
                    </div>

                    <!-- Image Preview Area -->
                    <div id="previewContainer" class="hidden relative">
                        <img id="imagePreview" src="" alt="Bukti Foto" class="max-h-64 mx-auto rounded-xl object-contain shadow-sm border border-slate-200">
                        <div class="mt-3 text-xs text-slate-500 flex items-center justify-center gap-2">
                            <span class="text-[#004d6d] font-semibold underline">Ganti Foto</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Lokasi (Mockup 3) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Lokasi <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="detectGPS()" class="text-xs text-[#004d6d] font-semibold hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span id="gpsStatusText">Gunakan GPS Saya</span>
                    </button>
                </div>

                <!-- Input with Location Pin Icon -->
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
                        value="{{ old('wilayah', 'Pantai Kenjeran, Surabaya') }}" 
                        required
                        placeholder="Pilih atau ketik wilayah pesisir di Indonesia..." 
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all bg-slate-50/30"
                    >
                    <datalist id="daftar-wilayah-datalist">
                        @foreach(\App\Helpers\WilayahHelper::allNames() as $namaPantai)
                            <option value="{{ $namaPantai }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Hidden Latitude and Longitude -->
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '-7.2275000') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '112.7985000') }}">
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Koordinat aktif: <span id="coordDisplay">-7.2275000, 112.7985000</span></span>
                    </span>
                    <span class="text-[10px] text-slate-400 italic">Mencakup pesisir seluruh Nusantara</span>
                </p>
            </div>

            <!-- 3. Kategori Sampah (Mockup 3) -->
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
                        <option value="" disabled {{ old('id_kategori') ? '' : 'selected' }}>Pilih Kategori Sampah</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori', 1) == $kategori->id_kategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Custom chevron down icon -->
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 4. Deskripsi (opsional) (Mockup 3) -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Deskripsi (opsional)
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Contoh: tumpukan sampah plastik di dekat tangga masuk pantai" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                >{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Submit Button (Mockup 3) -->
            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3.5 px-4 rounded-xl font-semibold text-sm shadow-sm cursor-pointer hover:shadow-md transition-all">
                    Kirim Laporan
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
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('previewContainer').classList.remove('hidden');
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
                    // Fallback to default Surabaya coordinates
                    document.getElementById('latitude').value = '-7.2275000';
                    document.getElementById('longitude').value = '112.7985000';
                    document.getElementById('coordDisplay').innerText = '-7.2275000, 112.7985000';
                    document.getElementById('displayLokasi').value = 'Pantai Kenjeran, Surabaya';
                    statusSpan.innerText = 'Kenjeran (Default)';
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
