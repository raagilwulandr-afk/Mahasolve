@extends('layouts.app')

@section('title', 'Katalog Layanan Mahasolve — Solusi Mobilitas & Akademik Unikom')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 space-y-8">

    <!-- HEADER & SEARCH BANNER (GOJEK STYLE MOBILITY BANNER) -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-3xl p-8 sm:p-10 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg width="350" height="350" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>

        <div class="relative z-10 space-y-5">
            <div class="max-w-2xl space-y-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md text-white font-bold text-[11px] rounded-full border border-white/20">
                        <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Layanan Kampus Terpercaya
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md text-white font-bold text-[11px] rounded-full border border-white/20">
                        <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Garansi Cepat &amp; Transparan
                    </span>
                </div>

                <h1 class="font-display font-extrabold text-2xl sm:text-4xl leading-tight tracking-tight">
                    Pesan Layanan Mahasiswa Unikom Cepat &amp; Praktis
                </h1>
                <p class="text-xs sm:text-sm text-indigo-100 leading-relaxed">
                    Pesan jasa antar jemput, cetak tugas, tutor bimbingan, hingga desain dalam satu aplikasi. Mitra terverifikasi siap melayani kebutuhan kampusmu.
                </p>
            </div>

            <!-- SEARCH BAR (FULL WIDTH / MENTOK KANAN) -->
            <form method="GET" class="relative pt-2 w-full">
                <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
                <div class="relative flex items-center w-full">
                    <svg class="absolute left-4 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama mitra atau jenis layanan {{ $kategoriAktif }}..."
                           class="w-full bg-white text-slate-800 border-0 rounded-2xl pl-12 pr-36 py-4 text-xs font-semibold shadow-lg focus:outline-none focus:ring-4 focus:ring-indigo-300/50">
                    <button type="submit" class="absolute right-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT GRID 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- KOLOM KIRI: SIDEBAR KATEGORI (3 COLS) -->
        <aside class="lg:col-span-3 bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm lg:sticky lg:top-24 space-y-3">
            <h3 class="px-2 text-xs font-extrabold text-slate-400 uppercase tracking-wider">Kategori Layanan</h3>

            <div class="space-y-1.5">
                @foreach ($kategoriList as $k)
                    @php $aktif = $kategoriAktif === $k['nama']; @endphp
                    <a href="{{ route('catalog.index', ['kategori' => $k['nama']]) }}"
                       class="flex items-center gap-3 px-3.5 py-3 rounded-2xl transition-all duration-200 {{ $aktif ? 'bg-indigo-50 border border-indigo-100 shadow-sm' : 'hover:bg-slate-50 border border-transparent' }}">
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background: {{ $k['warna'] }};">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">{!! $k['icon'] !!}</svg>
                        </span>
                        <span class="text-xs {{ $aktif ? 'font-bold text-indigo-700' : 'font-semibold text-slate-700' }}">
                            {{ $k['nama'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </aside>

        <!-- KOLOM KANAN: LIST PROVIDER (9 COLS) -->
        <main class="lg:col-span-9 space-y-4">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-base font-bold text-slate-900 font-display">
                    Mitra Penyedia <span class="text-indigo-600">"{{ $kategoriAktif }}"</span>
                </h2>
                <span class="text-xs text-slate-400 font-semibold">{{ $providers->count() }} Mitra Aktif</span>
            </div>

            <div class="space-y-4">
                @forelse ($providers as $provider)
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5">

                            <div class="flex items-start gap-4">
                                <div class="relative shrink-0">
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-display font-extrabold text-xl border border-indigo-100">
                                        {{ strtoupper(substr($provider->user->username, 0, 1)) }}
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white" title="Aktif Melayani"></span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-slate-900 text-base font-display">@ {{ $provider->user->username }}</h3>
                                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1 -18 0 9 9 0 0 1 18 0z"/>
                                            </svg>
                                            Mitra Terverifikasi
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $provider->detail_provider ?? 'Penyedia jasa terverifikasi Unikom dengan layanan cepat dan berkualitas.' }}</p>

                                    <div class="flex items-center gap-4 pt-1 flex-wrap text-xs text-slate-500">
                                        <span class="flex items-center gap-1 font-bold text-amber-500">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ number_format($provider->rating, 1) }}
                                            <span class="font-normal text-slate-400">({{ $provider->review_count }} Ulasan)</span>
                                        </span>
                                        <span class="flex items-center gap-1 font-medium text-slate-600">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            {{ $provider->order_count }} Pesanan Selesai
                                        </span>
                                        <span class="flex items-center gap-1 font-medium text-emerald-600 font-semibold">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Area Dipatiukur &amp; Kampus
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex sm:flex-col items-end sm:items-end justify-between gap-3 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">Tarif Mulai</span>
                                    <span class="text-lg font-black text-indigo-600 font-display">
                                        Rp{{ number_format($provider->harga_mulai ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('catalog.provider', $provider->id_provider) }}"
                                       class="px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-500/20 flex items-center gap-1.5 cursor-pointer">
                                        Pesan &amp; Nego Layanan &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-12 text-center space-y-2">
                        <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h4 class="font-bold text-slate-800 text-sm">Belum Ada Mitra Tersedia</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Belum ada mitra penyedia jasa untuk kategori "{{ $kategoriAktif }}".</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection
