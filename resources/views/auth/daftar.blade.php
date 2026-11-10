<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Relawan - Marine Rangers</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .bg-ocean-navy {
            background-color: #004d6d;
        }
        .btn-ocean {
            background-color: #004d6d;
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .btn-ocean:hover {
            background-color: #003a52;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Banner (Mockup 1) -->
        <div class="w-full lg:w-1/2 bg-ocean-navy text-white p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-sky-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/4 -right-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand -->
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-block">
                    <span class="font-serif-title text-2xl lg:text-3xl font-bold tracking-tight text-white hover:opacity-90">
                        Marine Rangers
                    </span>
                </a>
            </div>

            <!-- Center Content -->
            <div class="my-16 lg:my-auto max-w-lg relative z-10">
                <h1 class="font-serif-title text-3xl lg:text-5xl font-bold leading-tight mb-6">
                    Satu akun, untuk melapor dan menjaga laut bersama.
                </h1>
                <p class="text-white/80 text-sm lg:text-base leading-relaxed">
                    Masyarakat, komunitas lingkungan, pemerintah daerah, dan admin — semua terhubung dalam satu platform.
                </p>
            </div>

            <!-- Bottom Note -->
            <div class="text-xs text-white/60 relative z-10">
                Mendukung SDG 14 — Ekosistem Lautan
            </div>
        </div>

        <!-- Right Form Panel (Mockup 1 - Tab Daftar) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-white">
            <div class="w-full max-w-md">
                <!-- Navigation Tabs: Masuk / Daftar -->
                <div class="flex border-b border-slate-200 mb-8">
                    <a href="{{ route('login') }}" class="pb-3 text-sm font-medium text-slate-400 hover:text-slate-600 transition-colors mr-8">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="pb-3 text-sm font-semibold text-[#004d6d] border-b-2 border-[#004d6d] flex items-center gap-1">
                        Daftar
                    </a>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                        <div class="font-semibold mb-1">Gagal mendaftar:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Register Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="nama" 
                            name="nama" 
                            value="{{ old('nama') }}" 
                            required 
                            autocomplete="name"
                            placeholder="Contoh: Budi Santoso" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autocomplete="email"
                            placeholder="nama@email.com" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kata Sandi (Min. 8 Karakter)
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#004d6d]/20 focus:border-[#004d6d] transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-ocean w-full py-3.5 px-4 rounded-xl font-semibold text-sm shadow-sm cursor-pointer">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <!-- Footer Text -->
                <div class="text-center mt-8 text-xs text-slate-500">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-[#004d6d] font-semibold hover:underline">
                        Masuk di sini
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
