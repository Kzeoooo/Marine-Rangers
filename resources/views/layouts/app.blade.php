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
            --color-primary: #0c4a6e;
            --color-primary-hover: #075985;
            --color-primary-light: #e0f2fe;
            --color-accent-teal: #0d9488;
            --color-navy: #0c4a6e;
            --color-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 40%, #f1f5f9 100%);
            min-height: 100vh;
            color: #1e293b;
        }

        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%);
            color: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(12, 74, 110, 0.2);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #075985 0%, #0c4a6e 100%);
            box-shadow: 0 4px 12px rgba(12, 74, 110, 0.25);
        }

        .btn-outline-primary {
            border: 1.5px solid #0c4a6e;
            color: #0c4a6e;
            transition: all 0.2s ease;
        }
        .btn-outline-primary:hover {
            background-color: #e0f2fe;
            border-color: #075985;
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

        /* Essential Utility Safety Fallbacks */
        .hidden, [hidden] {
            display: none !important;
        }

        /* Hide scrollbar for clean tab scrolling */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Smooth Page Transition & Micro-Interactions */
        @keyframes pageEnter {
            0% {
                opacity: 0;
                transform: translateY(6px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-enter-active {
            animation: pageEnter 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Smooth Modal Pop Effect */
        @keyframes modalPop {
            0% {
                opacity: 0;
                transform: scale(0.96) translateY(4px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-anim {
            animation: modalPop 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Global Interactive Transitions */
        a, button, input, select, textarea {
            transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <!-- Slim Page Progress Indicator for App-like Smooth Transitions -->
    <div id="page-progress-bar" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-teal-400 via-sky-500 to-[#0c4a6e] z-[9999] transition-all duration-300 ease-out opacity-0 pointer-events-none" style="width: 0%;"></div>
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-sky-100/60 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Left: Logo & Role Subtitle -->
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <span class="font-serif-title text-2xl font-bold text-[#0c4a6e] tracking-tight group-hover:opacity-90">
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
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                    <a href="{{ url('/') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->is('/') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                        Beranda
                    </a>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('admin') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Panel Admin
                            </a>
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Kelola Laporan
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('admin.users.*') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Kelola Pengguna
                            </a>
                        @elseif(Auth::user()->isPemantau())
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Tugas Masuk
                            </a>
                        @else
                            <a href="{{ route('lapor') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('lapor') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Lapor Sampah
                            </a>
                            <a href="{{ route('laporan.index') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                                Laporan Saya
                            </a>
                        @endif

                        <a href="{{ route('peta') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('peta') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                            Peta Spasial
                        </a>
                        <a href="{{ route('statistik') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('statistik') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                            Statistik
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="transition-colors hover:text-[#0c4a6e]">
                            Lapor Sampah
                        </a>
                        <a href="{{ route('peta') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('peta') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                            Peta Sebaran
                        </a>
                        <a href="{{ route('statistik') }}" class="transition-colors hover:text-[#0c4a6e] {{ request()->routeIs('statistik') ? 'text-[#0c4a6e] font-semibold border-b-2 border-[#0369a1] pb-0.5' : '' }}">
                            Statistik
                        </a>
                        <button type="button" onclick="openSdgModal()" class="transition-colors hover:text-[#0c4a6e] text-left">
                            Tentang SDG 14
                        </button>
                    @endauth
                </nav>

                <!-- Right: Auth buttons or User Profile -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="btn-outline-primary px-5 py-2.5 rounded-xl text-sm font-semibold">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary px-5 py-2 rounded-lg text-sm font-semibold shadow-sm">
                            Daftar
                        </a>
                    @else
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button id="user-menu-btn" onclick="toggleUserDropdown()" class="flex items-center gap-3 p-1.5 rounded-full hover:bg-slate-100 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-800 font-semibold flex items-center justify-center text-sm border border-sky-200">
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
                                    <a href="{{ route('admin') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Panel Admin Sistem
                                    </a>
                                    <a href="{{ route('laporan.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Kelola Laporan
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        Kelola Akun Pengguna
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

                    <!-- Mobile Hamburger Button -->
                    <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-[#0c4a6e] hover:bg-slate-100 transition-colors" aria-label="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Contextual Role Feature Workspace Sub-Navbar -->
    <nav class="bg-white/95 backdrop-blur-md border-b border-sky-100 shadow-[0_1px_4px_rgba(12,74,110,0.03)] sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-13 overflow-x-auto no-scrollbar py-1 gap-4">
                <!-- Feature Action Pills by Role -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <!-- Admin: Dashboard Kontrol -->
                            <a href="{{ route('admin') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('admin') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Dashboard Admin</span>
                            </a>
                            <!-- Admin: Kelola Laporan -->
                            <a href="{{ route('laporan.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('laporan.*') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Kelola Laporan</span>
                            </a>
                            <!-- Admin: Kelola Pengguna -->
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Kelola Pengguna</span>
                            </a>
                            <!-- Admin: Peta Spasial -->
                            <a href="{{ route('peta') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('peta') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Peta Spasial</span>
                            </a>
                            <!-- Admin: Dashboard Statistik -->
                            <a href="{{ route('statistik') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('statistik') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Statistik</span>
                            </a>
                        @elseif(Auth::user()->isPemantau())
                            <!-- Pemantau: Tugas Masuk -->
                            <a href="{{ route('laporan.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('laporan.*') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Daftar Tugas Masuk</span>
                            </a>
                            <!-- Pemantau: Peta Spasial -->
                            <a href="{{ route('peta') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('peta') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Peta Spasial</span>
                            </a>
                            <!-- Pemantau: Statistik -->
                            <a href="{{ route('statistik') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('statistik') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Statistik Pesisir</span>
                            </a>
                        @else
                            <!-- Masyarakat: Lapor Sampah Baru -->
                            <a href="{{ route('lapor') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('lapor') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Lapor Sampah Baru</span>
                            </a>
                            <!-- Masyarakat: Laporan Saya -->
                            <a href="{{ route('laporan.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('laporan.*') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>Laporan Saya</span>
                            </a>
                            <!-- Masyarakat: Peta Sebaran -->
                            <a href="{{ route('peta') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('peta') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Peta Sebaran Sampah</span>
                            </a>
                            <!-- Masyarakat: Statistik -->
                            <a href="{{ route('statistik') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('statistik') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Statistik Pesisir</span>
                            </a>
                        @endif
                    @else
                        <!-- Guest: Beranda -->
                        <a href="{{ url('/') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->is('/') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Beranda</span>
                        </a>
                        <!-- Guest: Peta Sebaran -->
                        <a href="{{ route('peta') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('peta') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Peta Sebaran Sampah</span>
                        </a>
                        <!-- Guest: Dashboard Statistik -->
                        <a href="{{ route('statistik') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ request()->routeIs('statistik') ? 'bg-[#0c4a6e] text-white shadow-xs' : 'text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Dashboard Statistik</span>
                        </a>
                        <!-- Guest: SDG 14 -->
                        <button type="button" onclick="openSdgModal()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:text-[#0c4a6e] hover:bg-sky-50 transition-all">
                            <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tentang SDG 14</span>
                        </button>
                    @endauth
                </div>

                <!-- Right: Active Role Context Indicator -->
                <div class="hidden sm:flex items-center gap-2 shrink-0">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 border border-indigo-200/70 text-indigo-700">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>Administrator DLH</span>
                            </span>
                        @elseif(Auth::user()->isPemantau())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 border border-emerald-200/70 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Wilayah: {{ Auth::user()->wilayah_tugas ?? 'Pesisir Surabaya' }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 border border-sky-200/70 text-[#0c4a6e]">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                <span>Relawan Pesisir</span>
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-50 border border-slate-200/70 text-slate-600">
                            <span>🌊 Ekosistem Laut Lestari</span>
                        </span>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer Menu (Slide-Over) -->
    <div id="mobile-menu-backdrop" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 transition-opacity" onclick="toggleMobileMenu()"></div>
    <div id="mobile-menu-drawer" class="hidden fixed inset-y-0 right-0 max-w-xs w-full bg-white z-50 shadow-2xl flex flex-col p-6 overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <span class="font-serif-title text-xl font-bold text-[#0c4a6e]">Marine Rangers</span>
            <button type="button" onclick="toggleMobileMenu()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @auth
            <!-- User Info Badge in Mobile Menu -->
            <div class="py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-800 font-bold flex items-center justify-center text-sm border border-sky-200">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->nama }}</div>
                        <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full 
                        {{ Auth::user()->isAdmin() ? 'bg-indigo-100 text-indigo-700' : (Auth::user()->isPemantau() ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700') }}">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                    @if(Auth::user()->isPemantau() && Auth::user()->wilayah_tugas)
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ Auth::user()->wilayah_tugas }}
                        </span>
                    @endif
                </div>
            </div>
        @endauth

        <!-- Navigation Links in Mobile Menu -->
        <div class="py-4 space-y-1 text-sm font-medium text-slate-700 flex-grow">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->is('/') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Beranda</span>
            </a>

            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('admin') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Panel Admin</span>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Kelola Laporan</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.users.*') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Pengguna</span>
                    </a>
                @elseif(Auth::user()->isPemantau())
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Tugas Masuk</span>
                    </a>
                @else
                    <a href="{{ route('lapor') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('lapor') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Lapor Sampah</span>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('laporan.*') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Laporan Saya</span>
                    </a>
                @endif

                <a href="{{ route('peta') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('peta') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Peta Spasial</span>
                </a>
                <a href="{{ route('statistik') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('statistik') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Dashboard Statistik</span>
                </a>
            @else
                <a href="{{ route('peta') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('peta') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Peta Sebaran</span>
                </a>
                <a href="{{ route('statistik') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors {{ request()->routeIs('statistik') ? 'text-[#0c4a6e] font-bold bg-sky-50' : '' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Dashboard Statistik</span>
                </a>
                <button type="button" onclick="openSdgModal(); toggleMobileMenu();" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors text-left">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tentang SDG 14</span>
                </button>
            @endauth
        </div>

        <!-- Auth Actions in Mobile Menu -->
        <div class="pt-4 border-t border-slate-100">
            @guest
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn-outline-primary py-2.5 rounded-xl text-center text-xs font-semibold">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary py-2.5 rounded-xl text-center text-xs font-semibold shadow-sm">
                        Daftar
                    </a>
                </div>
            @else
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Keluar dari Sistem</span>
                    </button>
                </form>
            @endguest
        </div>
    </div>

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
    <main class="flex-grow page-enter-active">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-20 pt-12 pb-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                <!-- Col 1: Brand & Misi -->
                <div class="space-y-3">
                    <span class="font-serif-title font-bold text-[#0c4a6e] text-lg block">Marine Rangers</span>
                    <p class="text-slate-600 leading-relaxed text-xs">
                        Platform kolaboratif pemetaan sampah pesisir dan aksi penanganan terpadu untuk melindungi ekosistem lautan Surabaya mendukung SDG 14.
                    </p>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 text-[11px] font-semibold">
                        <span>🌊 Pesisir Bersih, Laut Lestari</span>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-[11px]">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/') }}" class="hover:text-[#0c4a6e] transition-colors">Beranda</a></li>
                        <li><a href="{{ route('peta') }}" class="hover:text-[#0c4a6e] transition-colors">Peta Sebaran Sampah</a></li>
                        <li><a href="{{ route('statistik') }}" class="hover:text-[#0c4a6e] transition-colors">Dashboard Statistik</a></li>
                        @auth
                            @if(Auth::user()->isMasyarakat())
                                <li><a href="{{ route('lapor') }}" class="hover:text-[#0c4a6e] transition-colors">Lapor Sampah Baru</a></li>
                                <li><a href="{{ route('laporan.index') }}" class="hover:text-[#0c4a6e] transition-colors">Riwayat Laporan Saya</a></li>
                            @elseif(Auth::user()->isPemantau())
                                <li><a href="{{ route('laporan.index') }}" class="hover:text-[#0c4a6e] transition-colors">Daftar Tugas Masuk</a></li>
                            @elseif(Auth::user()->isAdmin())
                                <li><a href="{{ route('admin') }}" class="hover:text-[#0c4a6e] transition-colors">Panel Admin Sistem</a></li>
                                <li><a href="{{ route('admin.users.index') }}" class="hover:text-[#0c4a6e] transition-colors">Kelola Akun Pengguna</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-[#0c4a6e] transition-colors">Masuk Relawan</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-[#0c4a6e] transition-colors">Daftar Akun Baru</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Col 3: Wilayah Binaan Pesisir -->
                <div>
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-[11px]">Wilayah Pengawasan</h4>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                        <li>• Pantai Kenjeran (THP)</li>
                        <li>• Kedung Cowek (Mangrove)</li>
                        <li>• Tambak Wedi (Suramadu)</li>
                        <li>• Pantai Ria Kenjeran</li>
                        <li>• Sukolilo Baru (Sentra Nelayan)</li>
                    </ul>
                </div>

                <!-- Col 4: Pusat Bantuan & Hotline -->
                <div>
                    <h4 class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-[11px]">Pusat Koordinasi</h4>
                    <p class="text-slate-600 mb-2 leading-relaxed text-xs">
                        Dinas Lingkungan Hidup Kota Surabaya & Relawan Pesisir.
                    </p>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] space-y-1">
                        <span class="font-semibold text-slate-700 block">Hotline Pengaduan Pesisir:</span>
                        <span class="text-[#0c4a6e] font-bold text-xs">112 Surabaya / (031) 5345678</span>
                        <span class="text-slate-400 block text-[10px]">Layanan Tanggap Cepat Insiden Laut</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px]">
                <p>© {{ date('Y') }} Marine Rangers · Pengembangan Perangkat Lunak (PPL) Surabaya.</p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="openSdgModal()" class="hover:text-[#0c4a6e] underline font-medium">Komitmen SDG 14</button>
                    <span>·</span>
                    <span>Sistem Spasial Pesisir</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- SDG 14 Modal -->
    <div id="sdg-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative modal-anim">
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

        function toggleMobileMenu() {
            const backdrop = document.getElementById('mobile-menu-backdrop');
            const drawer = document.getElementById('mobile-menu-drawer');
            if (backdrop && drawer) {
                backdrop.classList.toggle('hidden');
                drawer.classList.toggle('hidden');
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

        // App-like page navigation transition progress bar
        (function() {
            const bar = document.getElementById('page-progress-bar');
            if (!bar) return;

            function startProgress() {
                bar.style.transition = 'width 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease-in';
                bar.style.opacity = '1';
                bar.style.width = '75%';
            }

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                if (!href || href.startsWith('#') || href.startsWith('javascript:') || target === '_blank') return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                try {
                    const url = new URL(link.href, window.location.origin);
                    if (url.origin === window.location.origin && (url.pathname !== window.location.pathname || url.search !== window.location.search)) {
                        startProgress();
                    }
                } catch(err) {}
            });

            document.addEventListener('submit', function() {
                startProgress();
            });

            window.addEventListener('pageshow', function() {
                bar.style.opacity = '0';
                bar.style.width = '0%';
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
