<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marine Rangers - Jaga Laut Bersama')</title>

    <!-- Google Fonts: Serif & Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Leaflet CSS & JS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-primary: #004d6d;
            --color-primary-hover: #003b54;
            --color-primary-light: #e6f1f5;
            --color-accent-teal: #0284c7;
            --color-navy: #002b40;
            --color-bg: #f9fbfa;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--color-bg);
            color: #1e293b;
        }

        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: var(--color-primary-hover);
        }

        .btn-outline-primary {
            border: 1.5px solid var(--color-primary);
            color: var(--color-primary);
            transition: all 0.2s ease;
        }
        .btn-outline-primary:hover {
            background-color: var(--color-primary-light);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-sm border-b border-slate-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Left: Logo & Role Subtitle -->
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <span class="font-serif-title text-2xl font-bold text-[#004d6d] tracking-tight group-hover:opacity-90">
                            Marine Rangers
                        </span>
                    </a>

                    @auth
                        @if(Auth::user()->isPemantau())
                            <div class="hidden md:flex items-center text-xs text-slate-500 border-l border-slate-200 pl-4 py-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                                <span>Pemantau {{ Auth::user()->wilayah_tugas ? '· ' . Auth::user()->wilayah_tugas : '' }} · {{ Auth::user()->organisasi ?? 'Komunitas Peduli Laut' }}</span>
                            </div>
                        @elseif(Auth::user()->isAdmin())
                            <div class="hidden md:flex items-center text-xs text-slate-500 border-l border-slate-200 pl-4 py-1">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                                <span>Dinas Lingkungan Hidup · Administrator</span>
                            </div>
                        @else
                            <div class="hidden md:flex items-center text-xs text-slate-500 border-l border-slate-200 pl-4 py-1">
                                <span class="w-2 h-2 rounded-full bg-sky-500 mr-2"></span>
                                <span>Relawan Warga Pesisir</span>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Center: Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="{{ url('/') }}" class="transition-colors hover:text-[#004d6d] {{ request()->is('/') ? 'text-[#004d6d] font-semibold' : '' }}">
                        Beranda
                    </a>

                    @auth
                        @if(Auth::user()->isMasyarakat())
                            <a href="{{ route('lapor') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('lapor') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Lapor Sampah
                            </a>
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('laporan.index') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Laporan Saya
                            </a>
                        @elseif(Auth::user()->isPemantau())
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('laporan.index') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Tugas Masuk
                            </a>
                        @elseif(Auth::user()->isAdmin())
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('laporan.index') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Kelola Laporan
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('admin.users.*') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Kelola Pengguna
                            </a>
                            <a href="{{ route('admin') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('admin') ? 'text-[#004d6d] font-semibold' : '' }}">
                                Panel Admin
                            </a>
                        @endif
                        <a href="{{ route('peta') }}" class="transition-colors hover:text-[#004d6d] {{ request()->routeIs('peta') ? 'text-[#004d6d] font-semibold' : '' }}">
                            Peta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="transition-colors hover:text-[#004d6d]">
                            Lapor Sampah
                        </a>
                        <a href="{{ route('login') }}" class="transition-colors hover:text-[#004d6d]">
                            Peta
                        </a>
                        <button type="button" onclick="openSdgModal()" class="transition-colors hover:text-[#004d6d] text-left">
                            Tentang SDG 14
                        </button>
                    @endauth
                </nav>

                <!-- Right: Auth buttons or User Profile -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="btn-outline-primary px-5 py-2 rounded-lg text-sm font-semibold">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary px-5 py-2 rounded-lg text-sm font-semibold shadow-sm">
                            Daftar
                        </a>
                    @else
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button id="user-menu-btn" onclick="toggleUserDropdown()" class="flex items-center gap-3 p-1.5 rounded-full hover:bg-slate-100 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-[#e2f0f4] text-[#004d6d] font-semibold flex items-center justify-center text-sm border border-[#cbe4ec]">
                                    {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                                </div>
                                <div class="hidden lg:block text-left pr-2">
                                    <div class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->nama }}</div>
                                    <div class="text-[11px] text-slate-500 capitalize">{{ Auth::user()->role }}</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs text-slate-400">Masuk sebagai</p>
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full 
                                        {{ Auth::user()->isAdmin() ? 'bg-indigo-100 text-indigo-700' : (Auth::user()->isPemantau() ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700') }}">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                    @if(Auth::user()->isPemantau() && Auth::user()->wilayah_tugas)
                                        <span class="inline-block mt-1 ml-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            {{ Auth::user()->wilayah_tugas }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    Dashboard Saya
                                </a>

                                <a href="{{ route('peta') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    Peta Sebaran Sampah
                                </a>

                                @if(Auth::user()->isMasyarakat())
                                    <a href="{{ route('laporan.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Laporan Saya
                                    </a>
                                    <a href="{{ route('lapor') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Buat Laporan Baru
                                    </a>
                                @elseif(Auth::user()->isPemantau())
                                    <a href="{{ route('laporan.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Tugas Masuk
                                    </a>
                                @elseif(Auth::user()->isAdmin())
                                    <a href="{{ route('laporan.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Kelola Laporan
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Kelola Akun Pengguna
                                    </a>
                                    <a href="{{ route('admin') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Panel Admin Sistem
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 my-1"></div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Global Toast / Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-16 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-serif-title font-bold text-[#004d6d] text-base">Marine Rangers</span>
                <span>· Mendukung SDG 14 — Ekosistem Lautan</span>
            </div>
            <div>
                © {{ date('Y') }} Marine Rangers · Tim Pengembang Perangkat Lunak (PPL)
            </div>
        </div>
    </footer>

    <!-- SDG 14 Modal -->
    <div id="sdg-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
            <button onclick="closeSdgModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mb-4 text-xl font-bold">
                14
            </div>
            <h3 class="font-serif-title text-xl font-bold text-slate-900 mb-2">SDG 14: Life Below Water (Ekosistem Lautan)</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-4">
                Tujuan Pembangunan Berkelanjutan (SDG) 14 bertujuan untuk melestarikan dan memanfaatkan secara berkelanjutan sumber daya kelautan dan samudra untuk pembangunan berkelanjutan.
            </p>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
                <strong>Marine Rangers</strong> hadir untuk menghubungkan warga masyarakat, relawan pesisir, komunitas lingkungan, serta Dinas Lingkungan Hidup guna mendeteksi, memetakan, dan menindaklanjuti timbunan sampah pantai secara akurat dan berbasis data.
            </p>
            <button onclick="closeSdgModal()" class="btn-primary w-full py-2.5 rounded-lg text-sm font-semibold">
                Mengerti & Kembali
            </button>
        </div>
    </div>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-menu-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        function openSdgModal() {
            document.getElementById('sdg-modal')?.classList.remove('hidden');
        }

        function closeSdgModal() {
            document.getElementById('sdg-modal')?.classList.add('hidden');
        }

        // Close dropdown when clicked outside
        window.addEventListener('click', function(e) {
            const btn = document.getElementById('user-menu-btn');
            const dropdown = document.getElementById('user-menu-dropdown');
            if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
