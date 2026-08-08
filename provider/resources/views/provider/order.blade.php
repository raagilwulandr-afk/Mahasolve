<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasolve | Kelola Order & Negosiasi</title>

    <!-- Alpine AJAX & Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@imacraft/alpine-ajax@0.5.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* CSS Animasi Halaman Fluid (Alpine AJAX target) */
        .aria-busy {
            opacity: 0.65;
            transition: opacity 0.2s ease-in-out;
        }

        /* Animasi Smooth Fade-In saat perpindahan route/halaman */
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
</head>

<body class="bg-slate-50 text-slate-700 min-h-screen flex flex-col justify-between" x-data="orderApp()" x-target="main-content">

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

    <!-- MAIN CONTENT CONTAINER -->
    <main id="main-content" class="mx-auto max-w-7xl w-full px-6 py-10 space-y-8 flex-grow">

        <!-- FLASH NOTIFICATION -->
        @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex justify-between items-center shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        @endif

        <!-- HEADER BANNER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Pesanan Masuk</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola negosiasi & pesanan dari mahasiswa.</p>
            </div>
        </div>

        <!-- GRID ORDER & NEGO LAYOUT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- SIDEBAR DAFTAR ORDER (Kiri) -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-3">
                <template x-for="(order, index) in orders" :key="order.id">
                    <a :href="'{{ route('order') }}?active=' + order.raw_id"
                        :class="activeOrderIndex === index ? 'border-indigo-500 bg-indigo-50/40 shadow-sm' : 'border-slate-100 hover:border-indigo-200 hover:bg-slate-50/50'"
                        class="p-4 rounded-xl border-2 cursor-pointer transition flex items-center justify-between gap-3 block">

                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-white font-bold text-sm"
                                :class="order.avatarBg">
                                <span x-text="order.customerName.charAt(0)"></span>
                            </div>
                            <div class="truncate">
                                <h4 class="font-bold text-slate-800 text-sm truncate" x-text="order.title"></h4>
                                <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="order.customerName + ' • ' + order.date"></p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider block"
                                :class="{
                                    'bg-amber-100 text-amber-700': order.status === 'Negosiasi',
                                    'bg-sky-100 text-sky-700': order.status === 'Diproses',
                                    'bg-emerald-100 text-emerald-700': order.status === 'Selesai',
                                    'bg-rose-100 text-rose-700': order.status === 'Ditolak'
                                }"
                                x-text="order.status">
                            </span>
                        </div>
                    </a>
                </template>

                <template x-if="orders.length === 0">
                    <div class="py-12 text-center text-xs text-slate-400">Belum ada pesanan masuk.</div>
                </template>
            </div>

            <!-- DETAIL ORDER & NEGO CHAT (Kanan) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <template x-if="activeOrder">
                    <div>
                        <!-- BANNER STATUS ORDER -->
                        <div class="bg-gradient-to-r from-sky-500 via-indigo-600 to-indigo-700 p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-white/20 p-1 shrink-0 backdrop-blur-md">
                                    <div class="w-full h-full rounded-full bg-amber-300 flex items-center justify-center font-bold text-amber-900 text-lg"
                                        x-text="activeOrder.customerName.charAt(0)"></div>
                                </div>
                                <div>
                                    <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider backdrop-blur-md"
                                        x-text="'STATUS • ' + activeOrder.status"></span>
                                    <h2 class="text-xl font-bold mt-1 text-white" x-text="activeOrder.title"></h2>
                                    <p class="text-xs text-indigo-100 mt-0.5" x-text="activeOrder.customerName + ' • ' + activeOrder.category"></p>
                                </div>
                            </div>

                            <div class="sm:text-right border-t sm:border-t-0 border-white/10 pt-3 sm:pt-0">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-200 block">Penawaran</span>
                                <span class="text-2xl font-extrabold text-white" x-text="'Rp' + formatNumber(activeOrder.currentPrice)"></span>
                            </div>
                        </div>

                        <!-- INFO METADATA -->
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">ID Pesanan</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.id"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Tanggal</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.date"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Kategori</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.category"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Penawaran Mahasiswa</p>
                                    <p class="text-xs font-bold text-indigo-600 mt-0.5" x-text="'Rp' + formatNumber(activeOrder.customerOffer)"></p>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/50 p-3.5 rounded-xl border border-slate-100"
                                x-text="activeOrder.description"></p>

                            <!-- PERCAKAPAN NEGO CHAT BOX -->
                            <div class="border border-slate-100 rounded-2xl p-5 space-y-4">
                                <h3 class="font-bold text-slate-800 text-sm">Percakapan Negosiasi</h3>

                                <div class="space-y-3 max-h-80 overflow-y-auto p-1">
                                    <template x-for="chat in activeOrder.chats" :key="chat.id">
                                        <div :class="chat.sender === 'provider' ? 'flex flex-col items-end' : 'flex flex-col items-start'">
                                            <div :class="chat.sender === 'provider' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none'"
                                                class="p-3.5 max-w-sm text-xs leading-relaxed shadow-sm">
                                                <p x-text="chat.message"></p>
                                                <template x-if="chat.offeredPrice">
                                                    <div class="mt-2 pt-2 border-t text-[11px] font-bold flex justify-between gap-4"
                                                        :class="chat.sender === 'provider' ? 'border-white/20 text-amber-200' : 'border-slate-200 text-indigo-600'">
                                                        <span>Pengajuan Harga:</span>
                                                        <span x-text="'Rp' + formatNumber(chat.offeredPrice)"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-400 mt-1 px-1" x-text="chat.time"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- FORM BALAS CHAT -->
                                <template x-if="activeOrder.status === 'Negosiasi'">
                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/chat'" method="POST" class="flex gap-2 pt-2 border-t border-slate-100">
                                        @csrf
                                        <input type="text" name="pesan" required placeholder="Ketik balasan pesan..."
                                            class="flex-1 px-4 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                                            Kirim
                                        </button>
                                    </form>
                                </template>
                            </div>

                            <!-- ACTION BUTTONS LARAVEL -->
                            <template x-if="activeOrder.status === 'Negosiasi'">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/accept'" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-3 px-4 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                                            ✓ Terima Negosiasi
                                        </button>
                                    </form>

                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/reject'" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-3 px-4 border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                                            ✕ Tolak Negosiasi
                                        </button>
                                    </form>

                                    <button @click="openNegoModal = true" class="py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                                        ⟲ Ajukan Negosiasi
                                    </button>
                                </div>
                            </template>

                            <!-- STATUS FINAL BANNER -->
                            <template x-if="activeOrder.status !== 'Negosiasi'">
                                <div class="p-4 rounded-xl text-xs font-semibold text-center"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700 border border-emerald-200': activeOrder.status === 'Diproses' || activeOrder.status === 'Selesai',
                                        'bg-rose-50 text-rose-700 border border-rose-200': activeOrder.status === 'Ditolak'
                                    }">
                                    <span x-text="'Status pesanan ini telah ' + activeOrder.status.toLowerCase() + '.'"></span>
                                </div>
                            </template>

                        </div>
                    </div>
                </template>

                <template x-if="!activeOrder">
                    <div class="p-12 text-center text-slate-400 text-xs">
                        Silakan pilih pesanan untuk melihat detailnya.
                    </div>
                </template>
            </div>

        </div>

    </main>

    <!-- MODAL AJUKAN NEGO BALIK -->
    <div x-show="openNegoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-transition.opacity style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4" @click.away="openNegoModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-base">Ajukan Negosiasi Balik</h3>
                <button @click="openNegoModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="activeOrder ? '{{ url('/order') }}/' + activeOrder.raw_id + '/counter-nego' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Harga Tawaran Balik (Rp)</label>
                    <input type="number" name="harga_tawaran" x-model="counterPrice" required placeholder="Contoh: 45000"
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Penjelasan</label>
                    <textarea name="pesan" x-model="counterNote" rows="3" placeholder="Contoh: Masih bisa kak, kalau Rp45.000 pengerjaan lusa pagi sudah beres."
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openNegoModal = false" class="px-4 py-2 border border-slate-200 text-xs font-semibold text-slate-600 rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-xs font-semibold text-white rounded-xl hover:bg-indigo-700">Kirim Penawaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER (Sesuai Figma Dashboard & Layanan) -->
    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8 text-xs text-slate-500">
            <div class="space-y-3">
                <div class="flex items-center gap-2 font-bold text-indigo-600 text-base">
                    <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs">M</span> Mahasolve
                </div>
                <p class="text-slate-400 leading-relaxed">
                    Solusi mobilitas & akademik terpercaya untuk mahasiswa Unikom. Satu aplikasi untuk semua kebutuhan kampusmu.
                </p>
                <div class="flex items-center gap-3 pt-1 text-slate-400">
                    <a href="#" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition">📷</a>
                    <a href="#" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition">🐦</a>
                    <a href="#" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition">✉️</a>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Layanan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600 transition">Antar Jemput</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Print & Fotokopi</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Bimbingan</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Desain</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Perusahaan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600 transition">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Karier</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Blog</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3 text-sm">Bantuan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600 transition">Pusat Bantuan</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Jadi Mitra</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-100 py-6 text-center text-xs text-slate-400">
            © 2026 Mahasolve. Dibuat untuk mahasiswa Unikom.
        </div>
    </footer>

    <!-- prettier-ignore -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderApp', () => ({
                openNegoModal: false,
                counterPrice: '',
                counterNote: '',

                // Safe Blade JSON Mapping
                orders: [
                    @foreach($orders as $index => $item)
                    {
                        raw_id: {{ $item->id }},
                        id: 'ORD-{{ str_pad($item->id, 5, "0", STR_PAD_LEFT) }}',
                        title: '{{ $item->service->title ?? "Layanan" }}',
                        customerName: '{{ $item->customer->name ?? "Mahasiswa" }}',
                        date: '{{ $item->created_at->format("d M Y") }}',
                        status: '{{ $item->status }}',
                        category: '{{ $item->service->category ?? "Umum" }}',
                        currentPrice: {{ $item->service->price ?? 0 }},
                        customerOffer: {{ $item->negotiation_price ?? 0 }},
                        description: '{{ $item->notes ?? "Tidak ada catatan tambahan." }}',
                        avatarBg: ['bg-amber-400', 'bg-rose-400', 'bg-sky-400', 'bg-emerald-400', 'bg-indigo-400'][{{ $index }} % 5],
                        chats: [
                            @if($item->chats)
                                @foreach($item->chats as $chat)
                                {
                                    id: {{ $chat->id }},
                                    sender: '{{ $chat->sender }}',
                                    message: '{{ $chat->message }}',
                                    time: '{{ $chat->created_at->format("H:i") }}',
                                    offeredPrice: {{ $chat->offered_price ?? 'null' }}
                                }{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            @endif
                        ]
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],

                get activeOrderIndex() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const activeId = parseInt(urlParams.get('active'));
                    
                    if (activeId) {
                        const index = this.orders.findIndex(o => o.raw_id === activeId);
                        return index !== -1 ? index : (this.orders.length > 0 ? 0 : null);
                    }
                    return this.orders.length > 0 ? 0 : null;
                },

                get activeOrder() {
                    if (this.activeOrderIndex !== null) {
                        return this.orders[this.activeOrderIndex];
                    }
                    return null;
                },

                formatNumber(num) {
                    if (!num) return '0';
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }));
        });
    </script>
</body>

</html>