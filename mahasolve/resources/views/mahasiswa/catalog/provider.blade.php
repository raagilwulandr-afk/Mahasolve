@extends('layouts.app')

@section('title', 'Mitra Layanan ' . $provider->user->username . ' — Mahasolve')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 py-6">

    <!-- BREADCRUMB -->
    <div>
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
            &larr; Kembali ke Katalog Layanan
        </a>
    </div>

    <!-- HERO PROVIDER PROFILE CARD (GOJEK DRIVER/PARTNER STYLE HEADER) -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg width="300" height="300" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-display font-extrabold text-2xl border border-white/30 shrink-0">
                    {{ strtoupper(substr($provider->user->username, 0, 1)) }}
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl font-extrabold font-display leading-tight">@ {{ $provider->user->username }}</h1>
                        <span class="px-3 py-0.5 bg-emerald-500/20 backdrop-blur-md text-emerald-200 border border-emerald-400/30 text-[10px] font-extrabold rounded-full uppercase tracking-wider flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 0 0118 0z"/>
                            </svg>
                            Mitra Terverifikasi
                        </span>
                    </div>
                    <p class="text-xs text-indigo-100 max-w-lg">
                        {{ $provider->detail_provider ?? 'Penyedia jasa terverifikasi mahasiswa Unikom dengan jaminan pengerjaan cepat dan terpercaya.' }}
                    </p>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl text-right shrink-0">
                <span class="text-[11px] text-indigo-200 block font-semibold uppercase">Rating Mitra</span>
                <span class="text-xl font-black font-display text-amber-300 flex items-center justify-end gap-1">
                    <svg class="w-4 h-4 fill-current text-amber-300" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    {{ number_format($provider->rating, 1) }} / 5.0
                </span>
            </div>
        </div>
    </div>

    <!-- DAFTAR LAYANAN YANG DITAWARKAN (GOJEK SERVICE LISTING) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Pilihan Layanan Siap Pesan
            </h2>
            <span class="text-xs text-slate-400 font-semibold">{{ $provider->layanan->count() }} Pilihan Jasa</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($provider->layanan as $item)
                <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between hover:shadow-lg transition-all duration-300">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full border border-indigo-100">
                                {{ $item->kategori }}
                            </span>
                            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                Estimasi: {{ $item->estimasi_pengerjaan ?? '1 Hari' }}
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $item->nama_layanan }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $item->deskripsi }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">Harga Pas</span>
                            <span class="text-lg font-black text-indigo-600 font-display">Rp{{ number_format($item->harga, 0, ',', '.') }}</span>
                        </div>

                        <form method="POST" action="{{ route('catalog.direct-order') }}">
                            @csrf
                            <input type="hidden" name="id_layanan" value="{{ $item->id_layanan }}">
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition shadow-md shadow-indigo-500/20 cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Pesan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CUSTOM ORDER & NEGOSIASI SECTION (GOJEK NEGO ORDER STYLE) -->
    @php $openRequests = auth()->user()->requestLayanan()->where('status_request', 'open')->get(); @endphp

    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Ajukan Custom Order &amp; Negosiasi Harga
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Punya spesifikasi tugas atau anggaran khusus? Ajukan penawaran langsung ke mitra ini.</p>
        </div>

        @if ($openRequests->isEmpty())
            <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-500 flex items-center justify-between flex-wrap gap-3">
                <span>Kamu belum memiliki request layanan berstatus "open" untuk dinegosiasikan.</span>
                <a href="{{ route('mahasiswa.request.create', ['provider' => $provider->id_provider]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-sm">
                    + Buat Request Khusus Mitra Ini
                </a>
            </div>
        @else
            <form method="POST" action="" id="negosiasi-form" class="space-y-4 max-w-xl">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Request Layanan Kamu</label>
                    <select name="id_request" required
                            onchange="document.getElementById('negosiasi-form').action = '/request/' + this.value + '/negosiasi/{{ $provider->id_provider }}'"
                            class="w-full bg-slate-50 border border-slate-200/80 rounded-2xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Request Layanan --</option>
                        @foreach ($openRequests as $req)
                            <option value="{{ $req->id_request }}">{{ $req->kategori }} — {{ Str::limit($req->detail_kebutuhan, 40) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Penawaran Kamu (Rp)</label>
                    <input type="number" name="harga_tawaran" min="0" required placeholder="Contoh: 40000"
                           class="w-full bg-slate-50 border border-slate-200/80 rounded-2xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Tambahan (opsional)</label>
                    <textarea name="detail_negosiasi" rows="2" placeholder="Jelaskan detail penawaran atau batas waktu pengerjaan..."
                              class="w-full bg-slate-50 border border-slate-200/80 rounded-2xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition"></textarea>
                </div>

                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md shadow-indigo-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Penawaran Nego
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
