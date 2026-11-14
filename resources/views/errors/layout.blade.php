<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAFAF7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Marine Rangers</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen text-[#21295C] font-sans antialiased selection:bg-[#CFE8E4] selection:text-[#065A82]">
    <!-- Header Minimalis -->
    <header class="py-5 px-6 border-b border-[#E3E8E6] bg-white">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#065A82] text-white flex items-center justify-center">
                    <x-icon name="wave" class="w-5 h-5 text-[#CFE8E4]" />
                </div>
                <span class="font-serif font-bold text-xl text-[#065A82]">Marine Rangers</span>
            </a>

            <a href="{{ url('/') }}" class="text-sm font-medium text-gray-600 hover:text-[#065A82]">
                &larr; Ke Halaman Utama
            </a>
        </div>
    </header>

    <!-- Konten Utama Error Bertema Laut -->
    <main class="flex-grow flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full text-center">
            <!-- Lingkaran Ilustrasi Ikon Laut -->
            <div class="w-24 h-24 rounded-full bg-[#EAF4F8] text-[#065A82] border-2 border-[#CFE8E4] flex items-center justify-center mx-auto mb-6 shadow-sm">
                @yield('illustration')
            </div>

            <!-- Kode Error Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FAFAF7] border border-[#E3E8E6] text-xs font-bold text-[#065A82] uppercase tracking-wider mb-3">
                Error @yield('code')
            </div>

            <!-- Judul Error -->
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#21295C] mb-3">
                @yield('message')
            </h1>

            <!-- Deskripsi Ramah Bahasa Indonesia -->
            <p class="text-sm sm:text-base text-gray-600 leading-relaxed mb-8 max-w-md mx-auto">
                @yield('description')
            </p>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-button :href="url('/')" variant="primary" class="w-full sm:w-auto px-6 py-2.5">
                    Kembali ke Beranda
                </x-button>
                <x-button :href="route('peta')" variant="outline" class="w-full sm:w-auto px-6 py-2.5">
                    Buka Peta Sebaran
                </x-button>
            </div>
        </div>
    </main>

    <!-- Footer Mini dengan Ombak -->
    <footer class="bg-[#21295C] text-white relative">
        <x-wave fill="#21295C" :animated="false" class="h-6 sm:h-8" />
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-xs text-gray-300">
            &copy; {{ date('Y') }} Marine Rangers &bull; Mendukung SDG 14 - Ekosistem Lautan
        </div>
    </footer>
</body>
</html>
