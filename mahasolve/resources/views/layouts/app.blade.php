<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mahasolve — Solusi Mobilitas & Akademik Mahasiswa Unikom')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F8FC] text-[#16182B] antialiased min-h-screen flex flex-col justify-between" x-data="{ openMobileMenu: false }">

    <div>
        <!-- STRICT ROLE-BASED GO-LIVE HEADER NAVBAR -->
        <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
            <div class="max-w-[1280px] mx-auto px-6 h-16 flex items-center justify-between">
                @auth
                    <!-- LOGO & ROLE BRANDING -->
                    <div class="flex items-center gap-3">
                        <a href="{{ auth()->user()->role === 'provider' ? route('provider.dashboard') : route('catalog.index') }}" class="flex items-center gap-2.5 group">
                            <span class="w-9 h-9 rounded-2xl flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform"
                                  style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 12l6-8 4 5 6-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="18" cy="2" r="1.8" fill="#fff"/>
                                </svg>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="font-display font-extrabold text-xl tracking-tight text-slate-900 group-hover:text-indigo-600 transition">Mahasolve</span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full {{ auth()->user()->role === 'provider' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} uppercase tracking-wider">
                                    {{ auth()->user()->role === 'provider' ? 'Mitra Provider' : 'Mahasiswa' }}
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- STRICT ROLE-BASED DESKTOP NAVIGATION TABS -->
                    <div class="hidden md:flex items-center gap-1.5 text-xs font-semibold">
                        @if (auth()->user()->role === 'mahasiswa')
                            <!-- MENU KHUSUS AKUN MAHASISWA -->
                            <a href="{{ route('catalog.index') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('catalog.*') || request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Jelajah Layanan
                            </a>
                            <a href="{{ route('pesanan.index') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('pesanan.*') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9m12 0a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('review.index') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('review.*') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Riwayat &amp; Ulasan
                            </a>
                            <a href="{{ route('mahasiswa.request.create') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('mahasiswa.request.*') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Request Custom
                            </a>
                        @else
                            <!-- MENU KHUSUS AKUN PROVIDER -->
                            <a href="{{ route('provider.dashboard') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('provider.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('provider.dashboard') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Dashboard Provider
                            </a>
                            <a href="{{ route('my-service') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('my-service') || request()->routeIs('provider.my-service') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('my-service') || request()->routeIs('provider.my-service') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Layanan Saya
                            </a>
                            <a href="{{ route('order') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('order') || request()->routeIs('provider.order') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('order') || request()->routeIs('provider.order') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Order Masuk &amp; Nego
                            </a>
                            <a href="{{ route('provider.review') }}"
                               class="px-4 py-2 rounded-2xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('provider.review') || request()->routeIs('review') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('provider.review') || request()->routeIs('review') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Riwayat &amp; Ulasan
                            </a>
                        @endif
                    </div>

                    <!-- USER ACTIONS & NOTIFICATIONS -->
                    <div class="flex items-center gap-3">

                        <!-- NOTIFICATION PANEL -->
                        <div x-data="{ openNotif: false }" class="relative">
                            <button type="button" @click="openNotif = !openNotif" class="relative w-10 h-10 rounded-2xl flex items-center justify-center hover:bg-slate-100 transition cursor-pointer" title="Notifikasi">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4 15v-5a6 6 0 1112 0v5l1.5 2h-15L4 15z" stroke="#16182B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.5 17.5a1.5 1.5 0 003 0" stroke="#16182B" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-indigo-600 ring-2 ring-white"></span>
                            </button>

                            <div x-show="openNotif" @click.away="openNotif = false" x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white border border-slate-200/80 rounded-3xl shadow-xl p-5 z-50 space-y-3" style="display:none;">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-display">Notifikasi</h4>
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">Aktif</span>
                                </div>
                                <div class="space-y-2.5 max-h-64 overflow-y-auto">
                                    @if(auth()->user()->role === 'provider')
                                        <div class="p-3 bg-indigo-50/50 rounded-2xl border border-indigo-100/80 flex items-start gap-3">
                                            <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">Permintaan Jasa Baru</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Ada permintaan jasa baru dari mahasiswa yang menunggu penawaran Anda.</p>
                                            </div>
                                        </div>
                                        <div class="p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100/80 flex items-start gap-3">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">Negosiasi Disepakati</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Pesanan baru telah disepakati. Silakan kelola pengerjaan &amp; deliverable.</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-indigo-50/50 rounded-2xl border border-indigo-100/80 flex items-start gap-3">
                                            <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">Status Pesanan Diperbarui</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Mitra Provider sedang memproses pekerjaan permintaan Anda.</p>
                                            </div>
                                        </div>
                                        <div class="p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100/80 flex items-start gap-3">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">Deliverable Siap</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Hasil pengerjaan jasa telah dikirim dan siap diunduh.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- PROFILE AVATAR MENU (ROCK-SOLID DROPDOWN) -->
                        <div x-data="{ openProfile: false }" class="relative">
                            <button type="button" @click="openProfile = !openProfile" @mouseenter="openProfile = true"
                                    class="w-10 h-10 rounded-2xl flex items-center justify-center font-display font-bold text-sm border border-indigo-100 shadow-sm cursor-pointer hover:ring-2 hover:ring-indigo-300 transition"
                                    style="background:#EEF1FB; color:#4F46E5;">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </button>

                            {{-- CONTINUOUS CONTAINER (NO MOUSE GAP) --}}
                            <div x-show="openProfile"
                                 @mouseleave="openProfile = false"
                                 @click.away="openProfile = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute right-0 top-full pt-2 w-56 z-50"
                                 style="display: none;">
                                <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xl py-2 overflow-hidden">
                                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                                        <p class="text-xs font-extrabold text-slate-900">@ {{ auth()->user()->username }}</p>
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                        <span class="inline-block mt-1 text-[9px] font-bold px-2 py-0.5 rounded-full {{ auth()->user()->role === 'provider' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            Role: {{ ucfirst(auth()->user()->role) }}
                                        </span>
                                    </div>

                                    <div class="py-1">
                                        @if (auth()->user()->role === 'mahasiswa')
                                            <a href="{{ route('catalog.index') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Jelajah Layanan</a>
                                            <a href="{{ route('pesanan.index') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Pesanan Saya</a>
                                            <a href="{{ route('review.index') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Riwayat &amp; Ulasan</a>
                                        @else
                                            <a href="{{ route('provider.dashboard') }}" class="block px-5 py-2 text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition">Dashboard Provider</a>
                                            <a href="{{ route('my-service') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Kelola Layanan Saya</a>
                                            <a href="{{ route('order') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Order Masuk &amp; Nego</a>
                                            <a href="{{ route('provider.review') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Riwayat &amp; Ulasan</a>
                                        @endif

                                        <a href="{{ route('profile.edit') }}" class="block px-5 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">Pengaturan Profil</a>
                                    </div>

                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-1">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 cursor-pointer transition">Keluar (Logout)</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MOBILE HAMBURGER BUTTON -->
                        <button type="button" @click="openMobileMenu = !openMobileMenu" class="md:hidden p-2 rounded-2xl hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-2xl flex items-center justify-center shadow-md shadow-indigo-500/20"
                                  style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 12l6-8 4 5 6-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="18" cy="2" r="1.8" fill="#fff"/>
                                </svg>
                            </span>
                            <span class="font-display font-extrabold text-xl tracking-tight text-slate-900">Mahasolve</span>
                        </a>

                        <div class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600">
                            <a href="{{ route('home') }}#layanan" class="hover:text-indigo-600 transition">Layanan</a>
                            <a href="{{ route('home') }}#cara-kerja" class="hover:text-indigo-600 transition">Cara Kerja</a>
                            <a href="{{ route('home') }}#review" class="hover:text-indigo-600 transition">Review</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 transition shadow-md shadow-indigo-500/20">Daftar Akun</a>
                    </div>
                @endauth
            </div>

            <!-- MOBILE NAVIGATION DRAWER -->
            @auth
                <div x-show="openMobileMenu" @click.away="openMobileMenu = false" x-transition
                     class="md:hidden bg-white border-b border-slate-200/80 px-6 py-4 space-y-3" style="display:none;">
                    <div class="space-y-1">
                        @if (auth()->user()->role === 'mahasiswa')
                            <a href="{{ route('catalog.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50 hover:text-indigo-600">Jelajah Layanan</a>
                            <a href="{{ route('pesanan.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50 hover:text-indigo-600">Pesanan Saya</a>
                            <a href="{{ route('review.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50 hover:text-indigo-600">Riwayat &amp; Ulasan</a>
                            <a href="{{ route('mahasiswa.request.create') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50 hover:text-indigo-600">Request Custom</a>
                        @else
                            <a href="{{ route('provider.dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-indigo-600 hover:bg-indigo-50">Dashboard Provider</a>
                            <a href="{{ route('my-service') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50">Kelola Layanan Saya</a>
                            <a href="{{ route('order') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50">Order Masuk &amp; Nego</a>
                            <a href="{{ route('provider.review') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50">Riwayat &amp; Ulasan</a>
                        @endif
                    </div>
                </div>
            @endauth
        </nav>

        <main class="max-w-[1280px] mx-auto px-6 py-8">
            <x-breadcrumb />

            @if (session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 text-xs font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- UNIVERSAL FOOTER -->
    <footer class="bg-white border-t border-slate-200/80 py-8 mt-12">
        <div class="max-w-[1280px] mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[#6B6F85]">
            <div class="flex items-center gap-2">
                <span class="font-display font-extrabold text-sm text-[#16182B]">Mahasolve</span>
                <span>— Solusi Mobilitas &amp; Akademik Mahasiswa Unikom</span>
            </div>
            <div>
                © {{ date('Y') }} Mahasolve. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- SPA INSTANT 0MS PAGE NAVIGATOR & TOP PROGRESS BAR --}}
</body>
</html>
