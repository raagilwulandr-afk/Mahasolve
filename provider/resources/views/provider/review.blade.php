<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Provider Mahasolve</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50/50 min-h-screen font-sans antialiased text-slate-900">

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

    <!-- MAIN CONTENT -->
    <!-- PERBAIKAN 1: Sintaks x-data digabungkan dalam satu objek Javascript -->
    <!-- MAIN CONTENT -->
    <main id="main-content" 
        x-data="{ activeTab: 'semua', showReceiptModal: false, isLoaded: false }"
        x-init="setTimeout(() => isLoaded = true, 50)"
        :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
        class="mx-auto max-w-7xl px-6 py-10 space-y-8 transform transition-all duration-700 ease-out opacity-0 translate-y-4">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Riwayat Penyedia Jasa</h1>
                <p class="text-sm text-slate-500 mt-1">Pesanan yang telah selesai beserta pendapatan & ulasan dari mahasiswa.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Card 1 -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 text-xl shrink-0">📦</div>
                <div>
                    <p class="text-xl font-bold text-slate-900">{{ $stats->total_pesanan }}</p>
                    <p class="text-xs font-medium text-slate-500">Total pesanan</p>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-100 text-teal-600 text-xl shrink-0">💳</div>
                <div>
                    <p class="text-xl font-bold text-slate-900">Rp{{ number_format($stats->total_pendapatan, 0, ',', '.') }}</p>
                    <p class="text-xs font-medium text-slate-500">Total pendapatan</p>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-500 text-xl shrink-0">👍</div>
                <div>
                    <p class="text-xl font-bold text-slate-900">{{ $stats->rating }}</p>
                    <p class="text-xs font-medium text-slate-500">Rating diterima</p>
                </div>
            </div>
        </div>

        <!-- Filter Tabs (Sliding Fluid Animation) -->
        <div class="relative inline-grid grid-cols-3 items-center p-1 bg-white rounded-full border border-slate-200 shadow-sm w-full sm:w-[350px]">

            <!-- PERBAIKAN 2: Tag <div> pembuka di bawah ini sekarang sudah ditutup dengan tanda '>' -->
            <div
                class="absolute top-1 bottom-1 left-1 w-[calc((100%-8px)/3)] rounded-full bg-indigo-600 transition-all duration-300 ease-out"
                :style="
                    activeTab === 'semua' ? 'transform: translateX(0%);' : 
                    activeTab === 'selesai' ? 'transform: translateX(100%);' : 
                    'transform: translateX(200%);'
                ">
            </div>

            <!-- Button 1: Semua -->
            <button
                @click="activeTab = 'semua'"
                :class="activeTab === 'semua' ? 'text-white' : 'text-slate-500 hover:text-slate-800'"
                class="relative z-10 px-5 py-1.5 text-center text-sm font-semibold transition-colors duration-200">
                Semua
            </button>

            <!-- Button 2: Selesai -->
            <button
                @click="activeTab = 'selesai'"
                :class="activeTab === 'selesai' ? 'text-white' : 'text-slate-500 hover:text-slate-800'"
                class="relative z-10 px-5 py-1.5 text-center text-sm font-semibold transition-colors duration-200">
                Selesai
            </button>

            <!-- Button 3: Dibatalkan -->
            <button
                @click="activeTab = 'dibatalkan'"
                :class="activeTab === 'dibatalkan' ? 'text-white' : 'text-slate-500 hover:text-slate-800'"
                class="relative z-10 px-5 py-1.5 text-center text-sm font-semibold transition-colors duration-200">
                Dibatalkan
            </button>
        </div>

        <!-- History List -->
        <div class="space-y-4">
            @forelse($histories as $history)
            <!-- Alpine.js x-show for filtering with transition -->
            <div x-show="activeTab === 'semua' || activeTab === '{{ strtolower($history->status) }}'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">

                <!-- Transaction Header -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex gap-4">
                        <!-- Icon -->
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>

                        <!-- Title & Meta -->
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900">{{ $history->title }}</h3>
                                <!-- Status Badge -->
                                @if($history->status === 'Selesai')
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Selesai</span>
                                @else
                                <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-700">Dibatalkan</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z" />
                                </svg>
                                {{ $history->date }} · {{ $history->category }} · {{ $history->customer_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Income -->
                    <div class="text-left sm:text-right">
                        @if($history->status === 'Selesai')
                        <p class="text-lg font-bold text-indigo-600">Rp{{ number_format($history->income, 0, ',', '.') }}</p>
                        <p class="text-[10px] font-medium text-slate-400">Pendapatan bersih</p>
                        @else
                        <p class="text-sm font-bold text-slate-400 line-through">Rp{{ number_format($history->income, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Review Section (Hanya muncul jika Selesai & Punya Ulasan) -->
                @if($history->status === 'Selesai' && $history->has_review)
                <div class="mt-4 sm:ml-14 rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-200 text-xs font-bold text-indigo-700 shrink-0">
                            {{ substr($history->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Ulasan dari {{ $history->customer_name }}</p>
                            <!-- Stars -->
                            <div class="flex text-amber-400 text-[10px] mt-0.5">
                                @for($i = 0; $i < 5; $i++)
                                    @if($i < $history->rating)
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    @else
                                    <svg class="w-3.5 h-3.5 text-slate-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 italic">"{{ $history->review_text }}"</p>
                </div>
                @endif

                <!-- Action Buttons -->
                @if($history->status === 'Selesai')
                <div class="mt-4 sm:ml-14 flex items-center gap-3">
                    <button @click="showReceiptModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Struk
                    </button>
                    <button class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Balas Ulasan
                    </button>
                </div>
                @endif
            </div>
            @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                <p class="font-semibold text-slate-700">Belum ada riwayat pesanan.</p>
                <p class="mt-1 text-xs text-slate-400">Pesanan yang selesai atau dibatalkan akan muncul di sini.</p>
            </div>
            @endforelse
        </div> 

        <!-- Include Pop-up Struk -->
        @include('provider.receipt')

    </main>

    <!-- FOOTER -->
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