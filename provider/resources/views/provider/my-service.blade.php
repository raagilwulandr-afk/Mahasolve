<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasolve - Kelola Layanan</title>

    <!-- Alpine AJAX & Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@imacraft/alpine-ajax@0.5.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
</head>

<body x-data="{ openModal: false }" x-target="main-content" class="min-h-screen bg-slate-50 text-slate-700">

    <!-- NAVBAR (Presisi Sejajar Dashboard) -->
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

            <!-- Menu Navigasi -->
            <div class="hidden items-center gap-8 text-sm font-medium text-slate-500 md:flex">
                <a href="{{ route('dashboard') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('dashboard') || request()->is('dashboard*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('my-service') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('my-service') || request()->is('my-service*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Layanan
                </a>

                <a href="{{ route('order') }}"
                    class="transition hover:text-indigo-600 {{ (request()->routeIs('order') || request()->is('order*')) ? 'text-indigo-600 font-semibold' : '' }}">
                    Order
                </a>

                <a href="{{ route('review') }}"
                    class="transition hover:text-indigo-600 {{ request()->is('review*') ? 'text-indigo-600 font-semibold' : '' }}">
                    Riwayat
                </a>
            </div>

            <!-- Profile & Notification -->
            <div class="flex items-center gap-4">

                <!-- NOTIFIKASI DROPDOWN -->
                <div class="relative" x-data="{ openNotif: false }">
                    <button @click="openNotif = !openNotif"
                        type="button"
                        class="relative rounded-full p-2 text-slate-600 transition hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        @if(isset($notifications) && count($notifications) > 0)
                        <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="openNotif"
                        @click.away="openNotif = false"
                        x-transition
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

                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            @forelse($notifications ?? [] as $notif)
                            <a href="{{ $notif->link ?? '#' }}" class="p-4 flex items-start gap-3 hover:bg-slate-50 transition block">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-sm font-bold mt-0.5">📦</div>
                                <div class="space-y-1">
                                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->title ?? 'Pemberitahuan Baru' }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $notif->message ?? $notif->pesan }}</p>
                                    <span class="text-[10px] text-slate-400 block">{{ $notif->created_at ?? 'Baru saja' }}</span>
                                </div>
                            </a>
                            @empty
                            <div class="p-8 text-center flex flex-col items-center justify-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg">🔔</div>
                                <p class="text-xs font-semibold text-slate-700">Belum ada notifikasi baru</p>
                                <p class="text-[11px] text-slate-400 max-w-[200px]">Order masuk, negosiasi, dan ulasan akan muncul di sini.</p>
                            </div>
                            @endforelse
                        </div>

                        <div class="p-3 border-t border-slate-100 bg-slate-50/50 text-center">
                            <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">Lihat Semua Notifikasi</a>
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

    <!-- KONTEN UTAMA -->
    <main id="main-content" class="mx-auto max-w-7xl px-6 py-10 space-y-8">

        <!-- HEADER SECTION -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">PROVIDER CENTER</p>
                <h1 class="text-2xl font-bold text-slate-900 mt-1">Kelola Layanan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Atur daftar jasa, harga, dan portofolio layanan yang kamu tawarkan.</p>
            </div>
            <div>
                <button @click="openModal = true" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Layanan Baru
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- LAYANAN GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- LOOP DATA DARI DATABASE -->
            @forelse($services as $service)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between transition hover:-translate-y-1 hover:shadow-md h-full">
                <div>
                    <div class="h-44 bg-slate-100 relative flex items-center justify-center text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                            {{ $service->status ?? 'Aktif' }}
                        </span>
                    </div>

                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $service->kategori ?? 'Layanan' }}</span>
                            <span class="flex items-center gap-1 font-bold text-slate-700">
                                <span class="text-amber-400">★</span> 0.0 (0)
                            </span>
                        </div>

                        <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-1">
                            {{ $service->nama_layanan }}
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $service->deskripsi }}
                        </p>
                    </div>
                </div>

                <div class="p-5 pt-0 space-y-4">
                    <div class="flex items-center justify-between border-t border-slate-50 pt-3">
                        <span class="text-xs text-slate-400">Mulai dari</span>
                        <span class="text-base font-bold text-indigo-600">Rp{{ number_format($service->harga, 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button class="py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl transition">
                            Edit Layanan
                        </button>
                        <button class="py-2 border border-slate-200 hover:bg-red-50 text-red-600 font-semibold text-xs rounded-xl transition">
                            Nonaktifkan
                        </button>
                    </div>
                </div>
            </div>
            @empty
            {{-- Kosong --}}
            @endforelse

            <!-- KARTU TAMBAH LAYANAN -->
            <div @click="openModal = true"
                class="border-2 border-dashed border-slate-200 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center gap-3 bg-slate-50/50 hover:bg-white hover:border-indigo-300 transition group cursor-pointer min-h-[300px] w-full">
                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Buat Layanan Baru</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-[200px]">Tawarkan keahlian barumu kepada mahasiswa Unikom lainnya.</p>
                </div>
            </div>

        </div>

        <!-- STATISTIK SECTION -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">

            <div class="flex items-center gap-4 pt-4 md:pt-0">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 leading-none mb-1">{{ $totalLayanan ?? 0 }}</h3>
                    <p class="text-xs font-medium text-slate-400">Total layanan</p>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 leading-none mb-1">{{ $totalOrder ?? 0 }}</h3>
                    <p class="text-xs font-medium text-slate-400">Total order</p>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 leading-none mb-1">{{ number_format($rataRataRating ?? 0, 1) }}</h3>
                    <p class="text-xs font-medium text-slate-400">Rating rata-rata</p>
                </div>
            </div>

        </div>

    </main>

    <!-- MODAL FORM TAMBAH LAYANAN -->
    <div x-show="openModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto"
        x-transition.opacity
        style="display: none;">

        <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl relative my-auto max-h-[90vh] overflow-y-auto" @click.away="openModal = false">
            <div class="flex justify-between items-center mb-5 sticky top-0 bg-white z-10 pb-2 border-b border-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Layanan Baru</h3>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan</label>
                    <input type="text" name="nama_layanan" required placeholder="Contoh: Desain PPT Sidang Aesthetic"
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                    <select name="kategori" required class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                        <option value="Desain & Grafis">Desain & Grafis</option>
                        <option value="Editing Video">Editing Video</option>
                        <option value="Pengetikan & Format">Pengetikan & Format</option>
                        <option value="Tugas Coding">Tugas Coding</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Mulai Dari (Rp)</label>
                    <input type="number" name="harga" required placeholder="45000"
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Layanan</label>
                    <textarea name="deskripsi" rows="3" required placeholder="Jelaskan detail cakupan jasa yang kamu tawarkan..."
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-3 border-t border-slate-50">
                    <button type="button" @click="openModal = false"
                        class="w-full sm:w-auto px-4 py-2 border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition text-center">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition text-center">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>

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