@props([
    'title' => 'Autentikasi - Marine Rangers',
    'activeTab' => 'login', // 'login' or 'register'
    'totalLaporan' => null,
    'totalSelesai' => null,
])

@php
    $totalLaporan = $totalLaporan ?? \App\Models\Laporan::count();
    $totalSelesai = $totalSelesai ?? \App\Models\Laporan::where('status', \App\Models\Laporan::STATUS_SELESAI)->count();
    $hasPhoto = file_exists(public_path('images/login-pantai.jpg'));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAFAF7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-[#21295C] bg-[#FAFAF7] selection:bg-[#CFE8E4] selection:text-[#065A82]">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Panel Kiri: Visual Suasana Laut & Statistik Nyata -->
        <div class="relative w-full lg:w-1/2 min-h-[220px] sm:min-h-[280px] lg:min-h-screen bg-gradient-to-br from-[#065A82] via-[#1C7293] to-[#21295C] text-white flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-hidden">
            <!-- Background Image atau Fallback Gradient -->
            @if($hasPhoto)
                <img src="{{ asset('images/login-pantai.jpg') }}" 
                     alt="Pemandangan pesisir pantai laut Indonesia yang bersih" 
                     class="absolute inset-0 w-full h-full object-cover z-0" />
            @else
                <!-- Cadangan Ilustrasi Ombak Cantik jika foto belum ada -->
                <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 800 800">
                        <path d="M0,160 C150,220 350,100 500,180 C650,260 750,200 800,220 L800,800 L0,800 Z" fill="#CFE8E4" />
                        <path d="M0,320 C200,260 400,380 600,300 C700,260 750,320 800,300 L800,800 L0,800 Z" fill="#EAF4F8" opacity="0.5" />
                    </svg>
                </div>
            @endif

            <!-- Overlay Midnight Gradient (Memastikan Kontras Rasio >= 4.5:1) -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#21295C]/95 via-[#065A82]/85 to-[#065A82]/70 z-10"></div>

            <!-- Konten Header Kiri -->
            <div class="relative z-20">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-[#CFE8E4] flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <x-icon name="wave" class="w-6 h-6" />
                    </div>
                    <span class="font-serif font-bold text-2xl text-white tracking-tight">Marine Rangers</span>
                </a>
            </div>

            <!-- Konten Tengah: Kutipan Georgia -->
            <div class="relative z-20 my-auto py-8">
                <blockquote class="font-serif text-2xl sm:text-3xl lg:text-4xl font-medium leading-snug sm:leading-tight text-white max-w-lg">
                    “Satu akun, untuk melapor dan menjaga laut bersama.”
                </blockquote>
                <p class="mt-4 text-sm sm:text-base text-[#CFE8E4] max-w-md leading-relaxed">
                    Setiap laporan sampah yang kamu kirimkan menjadi kompas bagi relawan dan pemantau untuk aksi pembersihan nyata di pesisir Nusantara.
                </p>

                <!-- Kartu Kaca Statistik Nyata Database -->
                <div class="mt-8 grid grid-cols-2 gap-4 max-w-md bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 shadow-lg">
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-serif text-white flex items-baseline gap-1">
                            <span data-count-up="{{ $totalLaporan }}">{{ number_format($totalLaporan, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-xs sm:text-sm text-[#CFE8E4] mt-0.5">Total Laporan Warga</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-serif text-[#CFE8E4] flex items-baseline gap-1">
                            <span data-count-up="{{ $totalSelesai }}">{{ number_format($totalSelesai, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-xs sm:text-sm text-[#CFE8E4] mt-0.5">Aksi Pembersihan Tuntas</div>
                    </div>
                </div>
            </div>

            <!-- Konten Bawah: SDG 14 Badge & Pembatas Ombak -->
            <div class="relative z-20 flex items-center justify-between text-xs text-[#CFE8E4]">
                <div class="inline-flex items-center gap-2">
                    <x-icon name="shield-check" class="w-4 h-4 text-[#E8735A]" />
                    <span>Mendukung SDG 14 - Ekosistem Lautan</span>
                </div>
                <a href="{{ url('/') }}" class="hover:underline flex items-center gap-1">
                    Kembali ke Beranda &rarr;
                </a>
            </div>

            <!-- Ombak Pembatas di Tepi Bawah Kiri -->
            <div class="absolute bottom-0 left-0 right-0 z-10 pointer-events-none">
                <x-wave fill="#FAFAF7" :animated="false" class="h-6 sm:h-8" />
            </div>
        </div>

        <!-- Panel Kanan: Form Autentikasi -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 lg:p-16 relative">
            <div class="w-full max-w-md mx-auto fade-up">
                <!-- Tab Masuk | Daftar -->
                <div class="bg-[#EAF4F8] p-1.5 rounded-xl flex items-center mb-8 relative">
                    <a href="{{ route('login') }}" 
                       class="flex-1 text-center py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 {{ $activeTab === 'login' ? 'bg-white text-[#065A82] shadow-sm' : 'text-gray-600 hover:text-[#065A82]' }}">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="flex-1 text-center py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 {{ $activeTab === 'register' ? 'bg-white text-[#065A82] shadow-sm' : 'text-gray-600 hover:text-[#065A82]' }}">
                        Daftar Relawan
                    </a>
                </div>

                <!-- Form Content Slot -->
                {{ $slot }}

                <!-- Footer Kecil Form -->
                <div class="mt-8 text-center text-xs text-gray-500">
                    Dengan masuk atau mendaftar, kamu ikut berkontribusi dalam gerakan pelestarian ekosistem pantai dan laut Indonesia.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
