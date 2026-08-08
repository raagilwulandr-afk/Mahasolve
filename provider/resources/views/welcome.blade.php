<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasolve — Solusi Mobilitas & Akademik Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F8FC] text-[#16182B] antialiased">

    {{-- ================= NAVBAR ================= --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-[#14162B0E]">
        <div class="max-w-[1280px] mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-[14px] flex items-center justify-center shadow-sm"
                      style="background: linear-gradient(135deg, #4F46E5 0%, #14B8A6 100%);">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M2 12l6-8 4 5 6-7" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="18" cy="2" r="1.6" fill="#fff"/>
                    </svg>
                </span>
                <span class="font-display font-bold text-xl tracking-tight">Mahasolve</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm text-[#6B6F85]">
                <a href="#layanan" class="hover:text-[#16182B] transition">Layanan</a>
                <a href="#cara-kerja" class="hover:text-[#16182B] transition">Cara Kerja</a>
                <a href="#review" class="hover:text-[#16182B] transition">Review</a>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('catalog.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background:#4F46E5;">
                        Buka Aplikasi
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-[#16182B] hover:bg-[#F7F8FC] transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background:#4F46E5;">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ================= HERO ================= --}}
    <section class="relative overflow-hidden">
        <div class="absolute -top-20 right-0 w-[420px] h-[420px] rounded-full opacity-60 blur-3xl" style="background: rgba(79,70,229,0.1);"></div>
        <div class="absolute bottom-0 -left-20 w-[340px] h-[340px] rounded-full opacity-60 blur-3xl" style="background: rgba(20,184,166,0.1);"></div>

        <div class="relative max-w-[1280px] mx-auto px-6 py-24 grid md:grid-cols-2 gap-16 items-center">

            {{-- Image card + floating badges --}}
            <div class="relative order-2 md:order-1">
                <div class="rounded-[32px] border-[6px] border-white shadow-2xl overflow-hidden bg-gradient-to-br from-indigo-100 to-teal-100 aspect-[4/3]">
                    <div class="w-full h-full flex items-center justify-center text-[#4F46E5]/30">
                        <svg width="120" height="120" viewBox="0 0 24 24" fill="none"><path d="M4 16l4-5 3 3 5-6 4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>

                <div class="absolute -left-6 bottom-10 bg-white rounded-2xl shadow-xl border border-[#14162B14] p-4 flex flex-col items-center gap-3 w-[190px]">
                    <span class="w-11 h-11 rounded-[14px] flex items-center justify-center" style="background: rgba(22,163,74,0.15);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div class="text-center">
                        <p class="font-display font-bold text-sm">Pesanan Selesai</p>
                        <p class="text-xs text-[#6B6F85] mt-1">Rizky menerima orderanmu</p>
                    </div>
                </div>

                <div class="absolute right-6 -top-4 bg-white rounded-2xl shadow-xl border border-[#14162B14] p-3 flex items-center gap-1">
                    @for ($i = 0; $i < 5; $i++)
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="#F59E0B"><path d="M10 1.5l2.6 5.4 5.9.8-4.3 4.2 1 6-5.2-2.8-5.2 2.8 1-6-4.3-4.2 5.9-.8L10 1.5z"/></svg>
                    @endfor
                    <span class="font-semibold text-sm ml-1">4.9</span>
                </div>
            </div>

            {{-- Text content --}}
            <div class="order-1 md:order-2">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium mb-6"
                      style="background: rgba(79,70,229,0.05); border: 1px solid rgba(79,70,229,0.2); color:#4F46E5;">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 2l2 6h6l-5 4 2 6-5-4-5 4 2-6-5-4h6l2-6z" stroke="#4F46E5" stroke-width="1.3" stroke-linejoin="round"/></svg>
                    Platform #1 mahasiswa Unikom
                </span>

                <h1 class="font-display font-extrabold text-[40px] sm:text-[52px] leading-[1.05] tracking-tight">
                    Solusi Mobilitas &amp; Akademik Mahasiswa
                </h1>

                <p class="mt-6 text-lg text-[#6B6F85] max-w-md leading-relaxed">
                    Solusi mobilitas terpercaya bagi mahasiswa — pesan jasa antar jemput, print, bimbingan, hingga desain, semua dalam satu platform.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ auth()->check() ? route('catalog.index') : route('register') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium text-white" style="background:#4F46E5;">
                        Mulai
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4 10h12M11 5l5 5-5 5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-8 py-3 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                        Daftar Gratis
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap gap-6 text-sm text-[#6B6F85]">
                    <span class="flex items-center gap-1.5">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M3 10a7 7 0 1114 0 7 7 0 01-14 0z" stroke="#16A34A" stroke-width="1.3"/><path d="M7.5 10l1.8 1.8L12.5 8" stroke="#16A34A" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Tanpa biaya pendaftaran
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M3 10a7 7 0 1114 0 7 7 0 01-14 0z" stroke="#16A34A" stroke-width="1.3"/><path d="M7.5 10l1.8 1.8L12.5 8" stroke="#16A34A" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Harga transparan
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M3 10a7 7 0 1114 0 7 7 0 01-14 0z" stroke="#16A34A" stroke-width="1.3"/><path d="M7.5 10l1.8 1.8L12.5 8" stroke="#16A34A" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Provider terverifikasi
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= STATS BAR ================= --}}
    <section class="bg-white border-y border-[#14162B0E]">
        <div class="max-w-[1280px] mx-auto px-6 py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="font-display font-extrabold text-3xl" style="color:#4F46E5;">12.4K+</p>
                <p class="text-sm text-[#6B6F85] mt-1">Mahasiswa aktif</p>
            </div>
            <div>
                <p class="font-display font-extrabold text-3xl" style="color:#4F46E5;">850+</p>
                <p class="text-sm text-[#6B6F85] mt-1">Penyedia jasa</p>
            </div>
            <div>
                <p class="font-display font-extrabold text-3xl" style="color:#4F46E5;">48K+</p>
                <p class="text-sm text-[#6B6F85] mt-1">Transaksi selesai</p>
            </div>
            <div>
                <p class="font-display font-extrabold text-3xl" style="color:#4F46E5;">4.8/5</p>
                <p class="text-sm text-[#6B6F85] mt-1">Rating rata-rata</p>
            </div>
        </div>
    </section>

    {{-- ================= KATEGORI LAYANAN ================= --}}
    <section id="layanan" class="max-w-[1280px] mx-auto px-6 py-20">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight">Semua kebutuhan kampus, satu tempat</h2>
            <p class="mt-3 text-[#6B6F85] text-base">Pilih kategori layanan dan temukan penyedia jasa mahasiswa terpercaya.</p>
        </div>

        @php
            $kategori = [
                ['nama' => 'Antar Jemput', 'desc' => 'Ojek kampus & antar jemput cepat', 'warna' => '#4F46E5', 'icon' => '<path d="M4 16l4-5 3 3 5-6 4 5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
                ['nama' => 'Print & Fotokopi', 'desc' => 'Cetak tugas, jilid, scan', 'warna' => '#14B8A6', 'icon' => '<rect x="5" y="9" width="14" height="8" rx="1.5" stroke="#fff" stroke-width="2"/><path d="M7 9V4h10v5M7 17v3h10v-3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
                ['nama' => 'Bimbingan', 'desc' => 'Tutor sebaya & joki materi', 'warna' => '#F59E0B', 'icon' => '<path d="M4 6l8-3 8 3-8 3-8-3z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M6 10v6c0 1 2.7 2 6 2s6-1 6-2v-6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
                ['nama' => 'Desain & Editing', 'desc' => 'Poster, PPT, video, CV', 'warna' => '#EC4899', 'icon' => '<path d="M14 4l4 4-9 9-4.5.5.5-4.5L14 4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>'],
                ['nama' => 'Titip Makan', 'desc' => 'Jasa titip kantin & jajan', 'warna' => '#EF4444', 'icon' => '<path d="M6 3v7a2 2 0 002 2v9M6 3v5M8 3v5M10 3v5M10 3v18M18 3c-2 1-3 3-3 6s1 4 3 5v9" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>'],
                ['nama' => 'Titip Beli', 'desc' => 'Belikan kebutuhan harian', 'warna' => '#8B5CF6', 'icon' => '<path d="M6 8h12l-1 12H7L6 8z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M9 8V6a3 3 0 016 0v2" stroke="#fff" stroke-width="2"/>'],
            ];
        @endphp

        <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($kategori as $k)
                <div class="bg-white border border-[#14162B14] rounded-2xl p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                    <span class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm" style="background: {{ $k['warna'] }};">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">{!! $k['icon'] !!}</svg>
                    </span>
                    <h3 class="font-display font-bold text-lg mt-4">{{ $k['nama'] }}</h3>
                    <p class="text-sm text-[#6B6F85] mt-1">{{ $k['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= CARA KERJA ================= --}}
    <section id="cara-kerja" class="bg-white">
        <div class="max-w-[1280px] mx-auto px-6 py-20">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight">Cara kerjanya mudah</h2>
                <p class="mt-3 text-[#6B6F85] text-base">Tiga langkah dan kebutuhanmu beres.</p>
            </div>

            @php
                $langkah = [
                    ['no' => '1', 'judul' => 'Pilih layanan', 'desc' => 'Telusuri kategori & pilih penyedia jasa dengan rating terbaik.', 'icon' => '<path d="M9 8V6a3 3 0 016 0v2" stroke="#4F46E5" stroke-width="1.7"/><path d="M6 8h12l-1 12H7L6 8z" stroke="#4F46E5" stroke-width="1.7" stroke-linejoin="round"/>'],
                    ['no' => '2', 'judul' => 'Nego & sepakati', 'desc' => 'Chat langsung, negosiasi harga, dan sepakati detail pekerjaan.', 'icon' => '<path d="M4 5h11a2 2 0 012 2v6a2 2 0 01-2 2H9l-4 3v-3H4a2 2 0 01-2-2V7a2 2 0 012-2z" stroke="#4F46E5" stroke-width="1.7" stroke-linejoin="round"/>'],
                    ['no' => '3', 'judul' => 'Bayar aman', 'desc' => 'Pembayaran ditahan sampai pesanan selesai. Terima hasil, baru dilepas.', 'icon' => '<path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6l7-3z" stroke="#4F46E5" stroke-width="1.7" stroke-linejoin="round"/><path d="M9.5 12l1.8 1.8L14.5 10" stroke="#4F46E5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>'],
                ];
            @endphp

            <div class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach ($langkah as $l)
                    <div class="relative bg-white border border-[#14162B14] rounded-2xl p-8 overflow-hidden">
                        <span class="absolute top-4 right-6 font-display font-extrabold text-6xl select-none" style="color:#EEF1FB;">{{ $l['no'] }}</span>
                        <span class="relative w-12 h-12 rounded-[14px] flex items-center justify-center" style="background: rgba(79,70,229,0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">{!! $l['icon'] !!}</svg>
                        </span>
                        <h3 class="relative font-display font-bold text-xl mt-4">{{ $l['judul'] }}</h3>
                        <p class="relative text-[#6B6F85] mt-2 leading-relaxed">{{ $l['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONI ================= --}}
    <section id="review" class="max-w-[1280px] mx-auto px-6 py-20">
        <div class="text-center">
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight">Kata mereka tentang Mahasolve</h2>
        </div>

        @php
            $testimoni = [
                ['nama' => 'Ragil Yuni', 'peran' => 'Mahasiswa Informatika', 'rate' => 5, 'isi' => 'Cepat banget dapet jasa print pas deadline. Providernya ramah dan hasilnya rapi.'],
                ['nama' => 'Dhiya Al', 'peran' => 'Mahasiswa Informatika', 'rate' => 5, 'isi' => 'Fitur negosiasi keren, bisa nego harga langsung lewat chat sebelum deal.'],
                ['nama' => 'Raihan Ayalla', 'peran' => 'Mahasiswa DKV', 'rate' => 4, 'isi' => 'Banyak pilihan tutor sebaya. Bimbingan struktur data jadi lebih gampang dipahami.'],
            ];
        @endphp

        <div class="mt-12 grid md:grid-cols-3 gap-6">
            @foreach ($testimoni as $t)
                <div class="bg-white border border-[#14162B14] rounded-2xl p-6">
                    <div class="flex gap-1">
                        @for ($i = 0; $i < 5; $i++)
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="{{ $i < $t['rate'] ? '#F59E0B' : '#EEF0F6' }}"><path d="M10 1.5l2.6 5.4 5.9.8-4.3 4.2 1 6-5.2-2.8-5.2 2.8 1-6-4.3-4.2 5.9-.8L10 1.5z"/></svg>
                        @endfor
                    </div>
                    <p class="mt-4 text-[#16182B]/90 leading-relaxed">&ldquo;{{ $t['isi'] }}&rdquo;</p>
                    <div class="mt-6 flex items-center gap-3">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center font-display font-semibold text-sm" style="background:#EEF1FB; color:#4F46E5;">
                            {{ strtoupper(substr($t['nama'], 0, 1)) }}
                        </span>
                        <div>
                            <p class="font-semibold text-sm">{{ $t['nama'] }}</p>
                            <p class="text-xs text-[#6B6F85]">{{ $t['peran'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="max-w-[1280px] mx-auto px-6 pb-20">
        <div class="relative rounded-3xl px-8 py-14 text-center overflow-hidden"
             style="background: linear-gradient(135deg, #4F46E5 0%, #14B8A6 100%);">
            <div class="absolute -top-20 right-10 w-64 h-64 rounded-full bg-white/10"></div>

            <h2 class="relative font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight">Siap mulai perjalananmu?</h2>
            <p class="relative mt-3 text-white/90 max-w-xl mx-auto">
                Gabung ribuan mahasiswa Unikom yang sudah mempermudah kebutuhan kampusnya lewat Mahasolve.
            </p>

            <a href="{{ route('register') }}"
               class="relative inline-flex items-center gap-2 mt-8 px-6 py-3 rounded-full bg-white text-sm font-medium" style="color:#4F46E5;">
                Buat Akun Gratis
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4 10h12M11 5l5 5-5 5" stroke="#4F46E5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-white border-t border-[#14162B0E]">
        <div class="max-w-[1280px] mx-auto px-6 py-12 grid sm:grid-cols-2 md:grid-cols-4 gap-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-[14px] flex items-center justify-center" style="background: linear-gradient(135deg, #4F46E5 0%, #14B8A6 100%);">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M2 12l6-8 4 5 6-7" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="font-display font-bold text-xl">Mahasolve</span>
                </div>
                <p class="mt-3 text-sm text-[#6B6F85] max-w-xs">Solusi mobilitas &amp; akademik terpercaya untuk mahasiswa Unikom.</p>
            </div>

            <div>
                <h4 class="font-display font-semibold text-base">Layanan</h4>
                <ul class="mt-3 space-y-2 text-sm text-[#6B6F85]">
                    <li>Antar Jemput</li>
                    <li>Print &amp; Fotokopi</li>
                    <li>Bimbingan</li>
                    <li>Desain</li>
                </ul>
            </div>

            <div>
                <h4 class="font-display font-semibold text-base">Perusahaan</h4>
                <ul class="mt-3 space-y-2 text-sm text-[#6B6F85]">
                    <li>Tentang Kami</li>
                    <li>Karier</li>
                    <li>Blog</li>
                    <li>Kontak</li>
                </ul>
            </div>

            <div>
                <h4 class="font-display font-semibold text-base">Bantuan</h4>
                <ul class="mt-3 space-y-2 text-sm text-[#6B6F85]">
                    <li>Pusat Bantuan</li>
                    <li>Syarat &amp; Ketentuan</li>
                    <li>Privasi</li>
                    <li>Jadi Mitra</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[#14162B0E] py-5 text-center text-sm text-[#6B6F85]">
            &copy; {{ date('Y') }} Mahasolve. Dibuat untuk mahasiswa Unikom.
        </div>
    </footer>

</body>
</html>
