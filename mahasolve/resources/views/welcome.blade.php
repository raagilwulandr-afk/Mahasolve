@extends('layouts.app')

@section('title', 'Mahasolve — Solusi Mobilitas & Akademik Mahasiswa Unikom')

@section('content')
<div class="space-y-16 py-4">

    <!-- HERO SECTION -->
    <div class="grid lg:grid-cols-12 gap-12 items-center">
        <!-- LEFT: HERO TEXT -->
        <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-50 border border-indigo-100 rounded-full text-indigo-700 text-xs font-extrabold shadow-sm">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>Platform #1 Mobilitas & Akademik Mahasiswa Unikom</span>
            </div>

            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 leading-tight tracking-tight">
                Solusi Mobilitas &amp; Akademik Terpercaya Mahasiswa
            </h1>

            <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                Pesan jasa antar jemput kampus, cetak tugas skripsi, tutor bimbingan belajar, hingga desain grafis dalam satu platform terpadu. Mitra terverifikasi siap melayani kebutuhan kampusmu dengan cepat dan transparan.
            </p>

            <div class="flex items-center gap-3 pt-2 flex-wrap">
                <a href="{{ auth()->check() ? route('catalog.index') : route('register') }}"
                   class="px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold text-xs rounded-2xl transition shadow-lg shadow-indigo-500/25 flex items-center gap-2 cursor-pointer">
                    <span>Mulai Jelajah Layanan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="{{ route('register') }}"
                   class="px-6 py-3.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 font-extrabold text-xs rounded-2xl transition shadow-sm flex items-center gap-2">
                    Daftar Akun Gratis
                </a>
            </div>

            <div class="flex items-center gap-6 pt-4 text-xs font-semibold text-slate-500 flex-wrap">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tanpa Biaya Pendaftaran
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Harga Transparan &amp; Nego
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mitra Terverifikasi
                </span>
            </div>
        </div>

        <!-- RIGHT: HERO VISUAL MOCKUP CARD -->
        <div class="lg:col-span-5 relative flex items-center justify-center">
            <div class="relative rounded-3xl border-4 border-white shadow-2xl overflow-hidden bg-slate-50 transition-all duration-300 hover:shadow-indigo-500/10 w-full">
                <img src="{{ asset('images/hero_mockup.png') }}" alt="Mahasolve Student Mobility &amp; Courier Hero" class="w-full h-auto object-cover rounded-2xl">
            </div>

            <!-- FLOATING BADGES -->
            <div class="absolute -left-6 -bottom-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/80 p-3.5 flex items-center gap-3 w-52">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-display font-extrabold text-xs text-slate-900">Pesanan Selesai</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Mitra menerima orderanmu</p>
                </div>
            </div>

            <div class="absolute -right-4 top-6 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/80 p-3 flex items-center gap-1.5">
                <div class="flex text-amber-400">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="font-display font-extrabold text-xs text-slate-900">4.9</span>
            </div>
        </div>
    </div>

    <!-- STATS COUNTER BAR -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="space-y-1">
            <div class="font-display font-extrabold text-2xl sm:text-3xl text-indigo-600">12.4K+</div>
            <div class="text-xs font-semibold text-slate-500">Mahasiswa Aktif</div>
        </div>
        <div class="space-y-1">
            <div class="font-display font-extrabold text-2xl sm:text-3xl text-indigo-600">850+</div>
            <div class="text-xs font-semibold text-slate-500">Penyedia Jasa</div>
        </div>
        <div class="space-y-1">
            <div class="font-display font-extrabold text-2xl sm:text-3xl text-indigo-600">48K+</div>
            <div class="text-xs font-semibold text-slate-500">Transaksi Selesai</div>
        </div>
        <div class="space-y-1">
            <div class="font-display font-extrabold text-2xl sm:text-3xl text-indigo-600">4.8 / 5.0</div>
            <div class="text-xs font-semibold text-slate-500">Rating Rata-rata</div>
        </div>
    </div>

    <!-- KATEGORI LAYANAN -->
    <section id="layanan" class="space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">Semua Kebutuhan Kampus, Satu Tempat</h2>
            <p class="text-xs sm:text-sm text-slate-500">Pilih kategori layanan dan temukan penyedia jasa terverifikasi.</p>
        </div>

        @php
            $kategori = [
                ['nama' => 'Antar Jemput', 'desc' => 'Ojek motor & mobil kampus cepat', 'warna' => 'bg-indigo-600', 'badge' => 'Populer'],
                ['nama' => 'Print & Fotokopi', 'desc' => 'Cetak tugas, jilid skripsi & scan', 'warna' => 'bg-teal-600', 'badge' => 'Cepat'],
                ['nama' => 'Bimbingan', 'desc' => 'Tutor sebaya & bimbingan materi', 'warna' => 'bg-amber-500', 'badge' => 'Akademik'],
                ['nama' => 'Desain & Editing', 'desc' => 'Poster, PPT presentasi & video', 'warna' => 'bg-pink-600', 'badge' => 'Kreatif'],
                ['nama' => 'Titip Makan', 'desc' => 'Jasa titip jajan & makanan kantin', 'warna' => 'bg-rose-500', 'badge' => 'Kuliner'],
                ['nama' => 'Titip Beli', 'desc' => 'Titip beli perlengkapan harian', 'warna' => 'bg-purple-600', 'badge' => 'Praktis'],
            ];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($kategori as $k)
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 hover:shadow-lg hover:-translate-y-1 transition duration-200">
                    <div class="flex items-center justify-between">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-md {{ $k['warna'] }}">
                            {{ substr($k['nama'], 0, 1) }}
                        </span>
                        <span class="text-[10px] font-extrabold px-3 py-1 bg-slate-100 text-slate-700 rounded-full">
                            {{ $k['badge'] }}
                        </span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-base text-slate-900">{{ $k['nama'] }}</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $k['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CARA KERJA -->
    <section id="cara-kerja" class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 space-y-8 shadow-sm">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">Cara Kerja Mudah &amp; Transparan</h2>
            <p class="text-xs sm:text-sm text-slate-500">Tiga langkah simpel untuk menyelesaikan kebutuhanmu.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="space-y-3 relative p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-indigo-600 font-display font-extrabold text-3xl block">01</span>
                <h3 class="font-display font-bold text-sm text-slate-900">Pilih Layanan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Telusuri katalog atau buat request custom sesuai kebutuhanmu.</p>
            </div>
            <div class="space-y-3 relative p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-indigo-600 font-display font-extrabold text-3xl block">02</span>
                <h3 class="font-display font-bold text-sm text-slate-900">Negosiasi &amp; Deal</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Diskusi detail &amp; sepakati harga tawaran langsung di ruang chat.</p>
            </div>
            <div class="space-y-3 relative p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-indigo-600 font-display font-extrabold text-3xl block">03</span>
                <h3 class="font-display font-bold text-sm text-slate-900">Bayar &amp; Terima Hasil</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Bayar via QRIS aman. Mitra menyerahkan pekerjaan sesuai pesanan.</p>
            </div>
        </div>
    </section>

    <!-- TESTIMONI -->
    <section id="review" class="space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">Kata Mereka Tentang Mahasolve</h2>
            <p class="text-xs sm:text-sm text-slate-500">Pengalaman nyata mahasiswa Unikom dalam menggunakan platform.</p>
        </div>

        @php
            $testimoni = [
                ['nama' => 'Ragil Yuni', 'peran' => 'Mahasiswa Informatika', 'rate' => 5, 'isi' => 'Cepat banget dapet jasa print pas deadline skripsi. Providernya ramah dan hasilnya sangat rapi!'],
                ['nama' => 'Dhiya Al', 'peran' => 'Mahasiswa Informatika', 'rate' => 5, 'isi' => 'Fitur negosiasi sangat membantu, bisa nego harga langsung lewat chat sebelum deal transaksi.'],
                ['nama' => 'Raihan Ayalla', 'peran' => 'Mahasiswa DKV', 'rate' => 5, 'isi' => 'Banyak pilihan tutor sebaya. Bimbingan pemograman & struktur data jadi lebih mudah dipahami.'],
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($testimoni as $t)
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-sm flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center gap-1 text-amber-400">
                            @for ($i = 0; $i < $t['rate']; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed font-medium">&ldquo;{{ $t['isi'] }}&rdquo;</p>
                    </div>

                    <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 text-indigo-700 font-display font-extrabold text-xs flex items-center justify-center shrink-0 border border-indigo-100">
                            {{ strtoupper(substr($t['nama'], 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-display font-extrabold text-xs text-slate-900">{{ $t['nama'] }}</h4>
                            <p class="text-[10px] font-semibold text-slate-400 mt-0.5">{{ $t['peran'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CALL TO ACTION BANNER -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-3xl p-8 sm:p-12 text-white shadow-xl shadow-indigo-500/10 text-center space-y-4">
        <h2 class="font-display font-extrabold text-2xl sm:text-4xl leading-tight">
            Siap Mempermudah Kebutuhan Kampusmu?
        </h2>
        <p class="text-xs sm:text-sm text-indigo-100 max-w-xl mx-auto leading-relaxed">
            Bergabunglah bersama ribuan mahasiswa Unikom yang telah merasakan kemudahan layanan mobilitas &amp; akademik di Mahasolve.
        </p>
        <div class="pt-2">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-indigo-700 hover:bg-indigo-50 font-extrabold text-xs rounded-2xl shadow-lg transition cursor-pointer">
                <span>Daftar Akun Gratis Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

</div>
@endsection
