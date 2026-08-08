<!DOCTYPE html>
<html lang="id">
<!-- Alpine AJAX (Muat sebelum Alpine core atau ganti skrip alpine kamu) -->
<script defer src="https://cdn.jsdelivr.net/npm/@imacraft/alpine-ajax@0.5.0/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* CSS Animasi Halaman Fluid */
    .aria-busy {
        opacity: 0.6;
        transition: opacity 0.2s ease-in-out;
    }
    
    /* Animasi Smooth Fade-In saat konten baru masuk */
    main {
        animation: fadeIn 0.3s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasolve - Dashboard Provider</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data x-target="main-content" class="min-h-screen bg-slate-50 text-slate-700">

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <!-- Logo -->
            <a href="{{ route('provider.dashboard') }}" class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 font-bold text-white shadow-md">
                    M
                </div>
                <span class="text-lg font-bold text-slate-900">
                    Mahasolve
                </span>
            </a>

            <!-- Menu Navigasi (100% Akurat dengan Rute web.php) -->
            <div class="hidden items-center gap-8 text-sm font-medium text-slate-500 md:flex">

                <!-- Link Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('dashboard') || request()->is('dashboard*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Dashboard
                </a>

                <!-- Link Layanan -->
                <a href="{{ route('my-service') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('my-service') || request()->is('my-service*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Layanan
                </a>

                <!-- Link Order -->
                <a href="{{ route('order') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('order') || request()->is('order*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Order
                </a>

                <!-- Link Review -->
                <a href="{{ route('review') }}"
                    class="transition hover:text-indigo-600 {{ request()->is('review*') ? 'text-indigo-600 font-semibold' : '' }}">
                    Riwayat
                </a>

            </div>

            <!-- Profile & Notification -->
            <div class="flex items-center gap-4">

                <!-- DROPDOWN NOTIFIKASI -->
                <div class="relative" x-data="{ openNotif: false }">
                    <!-- Tombol Lonceng -->
                    <button @click="openNotif = !openNotif"
                        type="button"
                        class="relative rounded-full p-2 text-slate-600 transition hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        {{-- Badge Merah (Hanya Muncul Jika Ada Notifikasi Aktif) --}}
                        @if(isset($notifications) && count($notifications) > 0)
                        <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel Notifikasi -->
                    <div x-show="openNotif"
                        @click.away="openNotif = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl border border-slate-100 bg-white shadow-xl overflow-hidden z-50"
                        style="display: none;">

                        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800 text-sm">Notifikasi</h3>
                                @if(isset($notifications) && count($notifications) > 0)
                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    {{ count($notifications) }} Baru
                                </span>
                                @endif
                            </div>
                            <a href="#" class="text-xs text-indigo-600 font-semibold hover:underline">Tandai dibaca</a>
                        </div>

                        {{-- List Item Notifikasi --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            @forelse($notifications ?? [] as $notif)
                            <a href="{{ $notif->link ?? '#' }}" class="p-4 flex items-start gap-3 hover:bg-slate-50 transition block">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-sm font-bold mt-0.5">
                                    📦
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->title ?? 'Pemberitahuan Baru' }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $notif->message ?? $notif->pesan }}</p>
                                    <span class="text-[10px] text-slate-400 block">{{ $notif->created_at ?? 'Baru saja' }}</span>
                                </div>
                            </a>
                            @empty
                            {{-- State Jika Belum Ada Notifikasi Masuk --}}
                            <div class="p-8 text-center flex flex-col items-center justify-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg">
                                    🔔
                                </div>
                                <p class="text-xs font-semibold text-slate-700">Belum ada notifikasi baru</p>
                                <p class="text-[11px] text-slate-400 max-w-[200px]">Order masuk, pesan pelanggan, dan ulasan baru akan muncul di sini.</p>
                            </div>
                            @endforelse
                        </div>

                        <div class="p-3 border-t border-slate-100 bg-slate-50/50 text-center">
                            <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- PROFILE MENU -->
                <div class="relative" x-data="{ openProfile: false }">
                    <button @click="openProfile = !openProfile" class="flex items-center gap-2 rounded-full hover:bg-slate-100 p-1 transition">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-200 font-bold text-amber-800 text-sm">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'R', 0, 1)) }}
                        </div>
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openProfile" @click.away="openProfile = false" x-transition
                        class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-100 bg-white shadow-xl overflow-hidden z-50"
                        style="display: none;">
                        <div class="border-b border-slate-100 p-4">
                            <h3 class="font-semibold text-slate-800 text-sm">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Provider' }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ auth()->user()->email ?? 'user@mahasolve.id' }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50">👤 Edit Profile</a>
                        <a href="#" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50">📁 Portofolio</a>
                        <a href="#" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50">⚙️ Pengaturan</a>
                        <hr class="border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2.5 text-left text-xs text-rose-600 hover:bg-rose-50 font-semibold">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </nav>
    <!-- MAIN KONTEN -->
    <main id="main-content" class="mx-auto max-w-7xl px-6 py-10 space-y-8">

        <!-- ALERT NOTIFIKASI -->
        @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <p class="font-semibold">Ada kesalahan pada data:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- HEADER SECTION -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Penyedia Jasa</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola pesanan, negosiasi, dan pendapatanmu.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('my-service') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl transition shadow-sm">
                    Kelola Layanan
                </a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium text-sm rounded-xl transition">
                    Beralih ke Mode Mahasiswa
                </a>
            </div>
        </div>

        <!-- STATISTIK (4 STAT CARDS Sesuai Figma) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Pendapatan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">Rp{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400">Pendapatan bulan ini</div>
                </div>
            </div>

            <!-- Card 2: Pesanan Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">{{ $pesananAktif ?? 0 }}</div>
                    <div class="text-xs text-slate-400">Pesanan aktif</div>
                </div>
            </div>

            <!-- Card 3: Menunggu Nego / Pesanan Baru -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">{{ $pesananBaru ?? 0 }}</div>
                    <div class="text-xs text-slate-400">Menunggu nego</div>
                </div>
            </div>

            <!-- Card 4: Rating -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">{{ number_format($rating ?? 0, 1) }} <span class="text-amber-400">★</span></div>
                    <div class="text-xs text-slate-400">Rating</div>
                </div>
            </div>
        </div>

        <!-- MAIN LAYOUT 2 KOLOM (FIGMA LAYOUT) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- KOLOM KIRI: PERMINTAAN BARU (7 COLS) -->
            <section class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Permintaan Baru</h2>
                        <p class="text-xs text-slate-500">Pesanan yang menunggu respons kamu.</p>
                    </div>
                    <a href="{{ route('provider.requests.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($permintaanBaru as $matching)
                    @php
                    $requestLayanan = $matching->requestLayanan ?? null;
                    $mahasiswa = $requestLayanan?->mahasiswa ?? null;
                    @endphp

                    <article class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between gap-4 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($mahasiswa?->nama ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">
                                    {{ $mahasiswa?->nama ?? 'Mahasiswa' }}
                                </h3>
                                <p class="text-xs text-slate-500">
                                    {{ $requestLayanan?->judul_request ?? 'Permintaan Layanan' }}
                                </p>
                                <span class="text-[10px] text-slate-400">
                                    {{ $matching->tanggal_matching?->diffForHumans() ?? 'Baru saja' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-right mr-1">
                                <span class="text-[10px] text-slate-400 block">Budget</span>
                                <span class="font-bold text-sm text-indigo-600">
                                    Rp{{ number_format($requestLayanan?->harga_awal ?? 0, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Tombol Nego --}}
                                <a href="{{ route('provider.requests.show', $matching->id ?? 1) }}"
                                    class="px-3 py-1.5 border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-lg transition flex items-center gap-1">
                                    💬 Nego
                                </a>

                                {{-- Form & Tombol Terima --}}
                                <form method="POST" action="{{ route('provider.requests.accept', $matching->id ?? 1) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                        Terima
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                        <p class="font-semibold text-slate-700">Belum ada permintaan baru.</p>
                        <p class="mt-1 text-xs text-slate-400">Permintaan mahasiswa akan muncul di sini.</p>
                    </div>
                    @endforelse
                </div>
            </section>

            <!-- KOLOM KANAN: LAYANAN AKTIF (5 COLS) -->
            <section class="lg:col-span-5 space-y-4">
                <h2 class="text-lg font-bold text-slate-900">Layanan Aktif</h2>

                @if ($layananAktif && isset($layananAktif->id))
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($layananAktif->mahasiswa?->nama ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">
                                {{ $layananAktif->mahasiswa?->nama ?? 'Mahasiswa' }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ $layananAktif->layanan?->nama_layanan ?? 'Jasa Aktif' }}
                            </p>
                        </div>
                    </div>

                    <!-- Progress & Status -->
                    <div class="space-y-2 border-t border-slate-50 pt-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-600">
                                ⏱ {{ $layananAktif->trackingTerbaru?->status_pengerjaan ?? 'Sedang diproses' }}
                            </span>
                            <span class="font-bold text-sm text-indigo-600">
                                Rp{{ number_format($layananAktif->total_harga ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                                style="width: {{ match ($layananAktif->status_pesanan ?? '') {
                                        'menunggu pembayaran' => '20%',
                                        'diproses' => '60%',
                                        'revisi' => '80%',
                                        'selesai' => '100%',
                                        default => '30%'
                                    } }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Selesai -->
                    <form method="POST" action="{{ route('orders.complete', $layananAktif) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            onclick="return confirm('Tandai pekerjaan ini sebagai selesai?')"
                            class="w-full py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center justify-center gap-1">
                            ✓ Tandai Selesai
                        </button>
                    </form>
                </div>
                @else
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                    <p class="font-semibold text-slate-700">Belum ada layanan aktif.</p>
                    <p class="mt-1 text-xs text-slate-400">Pesanan yang sedang dikerjakan akan muncul di sini.</p>
                </div>
                @endif
            </section>

        </div>

    </main>

    <!-- FOOTER (Sesuai Figma) -->
    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8 text-xs text-slate-500">
            <div class="space-y-3">
                <div class="flex items-center gap-2 font-bold text-indigo-600 text-base">
                    <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs">M</span> Mahasolve
                </div>
                <p class="text-slate-400 leading-relaxed">
                    Solusi mobilitas & akademik terpercaya untuk mahasiswa Unikom. Satu aplikasi untuk semua kebutuhan kampusmu.
                </p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Layanan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600">Antar Jemput</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Print & Fotokopi</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Bimbingan</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Desain</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Perusahaan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Karier</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Blog</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Bantuan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600">Pusat Bantuan</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-indigo-600">Jadi Mitra</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-100 py-6 text-center text-xs text-slate-400">
            © 2026 Mahasolve. Dibuat untuk mahasiswa Unikom.
        </div>
    </footer>

</body>

</html>