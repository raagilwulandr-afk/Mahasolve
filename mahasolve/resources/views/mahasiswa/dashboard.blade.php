@extends('layouts.app')

@section('title', 'Home — Mahasolve')

@section('content')

    {{-- ================= HERO SAPAAN ================= --}}
    <section class="relative overflow-hidden rounded-3xl p-8 sm:p-12"
             style="background: linear-gradient(135deg, #4F46E5 0%, #14B8A6 100%);">
        <div class="absolute -top-16 right-24 w-64 h-64 rounded-full bg-white/10"></div>
        <div class="absolute top-24 right-4 w-72 h-72 rounded-full bg-white/5"></div>

        <div class="relative max-w-xl">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm text-white bg-white/15">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 2l1.5 3.5L15 7l-3.5 1.5L10 12l-1.5-3.5L5 7l3.5-1.5L10 2z" stroke="#fff" stroke-width="1.3" stroke-linejoin="round"/></svg>
                Halo, {{ auth()->user()->username }}!
            </span>

            <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-white mt-4 tracking-tight">
                Butuh bantuan hari ini?
            </h1>

            <p class="mt-4 text-white/90 leading-relaxed">
                Pesan layanan dari penyedia jasa mahasiswa terbaik di kampusmu, cepat dan terpercaya.
            </p>

            <a href="{{ route('catalog.index') }}"
               class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-full bg-white text-sm font-medium" style="color:#4F46E5;">
                Pesan Sekarang
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4 10h12M11 5l5 5-5 5" stroke="#4F46E5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>

    {{-- ================= KATEGORI LAYANAN ================= --}}
    <section class="mt-10">
        <h2 class="font-display font-bold text-xl">Kategori Layanan</h2>

        @php
            $kategori = [
                ['nama' => 'Antar Jemput', 'warna' => '#4F46E5', 'icon' => '<path d="M4 16l4-5 3 3 5-6 4 5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
                ['nama' => 'Print & Fotokopi', 'warna' => '#14B8A6', 'icon' => '<rect x="5" y="9" width="14" height="8" rx="1.5" stroke="#fff" stroke-width="2"/><path d="M7 9V4h10v5M7 17v3h10v-3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
                ['nama' => 'Bimbingan', 'warna' => '#F59E0B', 'icon' => '<path d="M4 6l8-3 8 3-8 3-8-3z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M6 10v6c0 1 2.7 2 6 2s6-1 6-2v-6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
                ['nama' => 'Desain & Editing', 'warna' => '#EC4899', 'icon' => '<path d="M14 4l4 4-9 9-4.5.5.5-4.5L14 4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>'],
                ['nama' => 'Titip Makan', 'warna' => '#EF4444', 'icon' => '<path d="M6 3v7a2 2 0 002 2v9M6 3v5M8 3v5M10 3v5M10 3v18M18 3c-2 1-3 3-3 6s1 4 3 5v9" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>'],
                ['nama' => 'Titip Beli', 'warna' => '#8B5CF6', 'icon' => '<path d="M6 8h12l-1 12H7L6 8z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M9 8V6a3 3 0 016 0v2" stroke="#fff" stroke-width="2"/>'],
            ];
        @endphp

        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($kategori as $k)
                <a href="{{ route('catalog.index', ['kategori' => $k['nama']]) }}"
                   class="bg-white border border-[#14162B14] rounded-2xl p-4 flex flex-col items-center gap-2 text-center hover:shadow-md hover:-translate-y-0.5 transition">
                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: {{ $k['warna'] }};">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">{!! $k['icon'] !!}</svg>
                    </span>
                    <span class="text-xs font-medium">{{ $k['nama'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= PESANAN AKTIF & RIWAYAT ================= --}}
    <div class="mt-10 grid lg:grid-cols-2 gap-8">

        {{-- Pesanan Aktif --}}
        <section>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-bold text-xl">Pesanan Aktif</h2>
                <a href="{{ route('pesanan.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium text-[#16182B] hover:bg-white transition">Lihat semua</a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($aktivitasAktif as $item)
                    <a href="{{ $item->url }}" class="flex items-center gap-4 bg-white border border-[#14162B14] rounded-2xl p-4 hover:shadow-sm transition">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center font-display font-semibold text-sm shrink-0"
                              style="background:#EEF1FB; color:#4F46E5;">
                            {{ strtoupper(substr($item->nama_lawan, 0, 1)) }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold text-sm truncate">{{ Str::limit($item->judul, 32) }}</p>
                                <span @class([
                                    'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                                    'bg-[#4F46E51A] text-[#4F46E5]' => $item->badge_color === 'indigo',
                                    'bg-[#F59E0B26] text-[#F59E0B]' => $item->badge_color === 'amber',
                                ])>
                                    {{ $item->badge }}
                                </span>
                            </div>
                            <p class="text-sm text-[#6B6F85] mt-0.5 truncate">{{ $item->nama_lawan }} · {{ $item->kode }}</p>
                        </div>

                        <span class="shrink-0 px-3 py-1.5 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC]">
                            {{ $item->badge_color === 'amber' ? 'Negosiasi' : 'Detail' }}
                        </span>
                    </a>
                @empty
                    <div class="bg-white border border-[#14162B14] rounded-2xl p-8 text-center">
                        <p class="text-sm text-[#6B6F85]">Belum ada pesanan aktif.</p>
                        <a href="{{ route('catalog.index') }}" class="inline-block mt-2 text-sm font-medium" style="color:#4F46E5;">Cari layanan sekarang &rarr;</a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Riwayat --}}
        <section>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-bold text-xl">Riwayat</h2>
                <a href="{{ route('pesanan.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium text-[#16182B] hover:bg-white transition">Lihat semua</a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($riwayat as $p)
                    <a href="{{ route('pesanan.show', $p->id_pesanan) }}" class="flex items-center gap-4 bg-white border border-[#14162B14] rounded-2xl p-4 hover:shadow-sm transition">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" style="background:#EEF1FB;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ Str::limit($p->negosiasi->request->detail_kebutuhan, 32) }}</p>
                            <p class="text-sm text-[#6B6F85] mt-0.5 flex items-center gap-1">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="#6B6F85" stroke-width="1.2"/><path d="M3 8h14M7 2v4M13 2v4" stroke="#6B6F85" stroke-width="1.2"/></svg>
                                {{ $p->tanggal_pesanan->translatedFormat('d M Y') }} · Rp{{ number_format($p->harga_final, 0, ',', '.') }}
                            </p>
                        </div>

                        <span class="shrink-0 px-3 py-1.5 rounded-full text-sm font-medium">Struk</span>
                    </a>
                @empty
                    <div class="bg-white border border-[#14162B14] rounded-2xl p-8 text-center">
                        <p class="text-sm text-[#6B6F85]">Belum ada riwayat pesanan selesai.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

@endsection
