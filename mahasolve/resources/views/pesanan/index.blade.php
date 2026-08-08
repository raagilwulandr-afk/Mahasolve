@extends('layouts.app')

@section('title', 'Order')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-display font-extrabold text-3xl tracking-tight">Pesanan Saya</h1>
            <p class="text-xs text-[#6B6F85] mt-1">Kelola transaksi aktif, pelacakan pengerjaan, dan berikan ulasan mitra.</p>
        </div>

        {{-- TAB FILTERS --}}
        <div class="flex gap-1.5 flex-wrap">
            <a href="{{ route('pesanan.index', ['status' => 'semua']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ ($filterStatus ?? 'semua') === 'semua' ? 'bg-[#4F46E5] text-white' : 'bg-[#EEF1FB] text-[#6B6F85] hover:bg-[#E2E7F8]' }}">
                Semua
            </a>
            <a href="{{ route('pesanan.index', ['status' => 'negosiasi']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-medium transition {{ ($filterStatus ?? '') === 'negosiasi' ? 'bg-[#4F46E5] text-white' : 'bg-[#EEF1FB] text-[#6B6F85] hover:bg-[#E2E7F8]' }}">
                Negosiasi Aktif
            </a>
            <a href="{{ route('pesanan.index', ['status' => 'diproses']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-medium transition {{ ($filterStatus ?? '') === 'diproses' ? 'bg-[#4F46E5] text-white' : 'bg-[#EEF1FB] text-[#6B6F85] hover:bg-[#E2E7F8]' }}">
                Diproses
            </a>
            <a href="{{ route('pesanan.index', ['status' => 'selesai']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-medium transition {{ ($filterStatus ?? '') === 'selesai' ? 'bg-[#4F46E5] text-white' : 'bg-[#EEF1FB] text-[#6B6F85] hover:bg-[#E2E7F8]' }}">
                Selesai &amp; Ulasan
            </a>
            <a href="{{ route('pesanan.index', ['status' => 'dibatalkan']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-medium transition {{ ($filterStatus ?? '') === 'dibatalkan' ? 'bg-[#4F46E5] text-white' : 'bg-[#EEF1FB] text-[#6B6F85] hover:bg-[#E2E7F8]' }}">
                Dibatalkan
            </a>
        </div>
    </div>

    @if ($daftarAktivitas->isEmpty())
        <div class="bg-white border border-[#14162B14] rounded-2xl p-10 text-center">
            <p class="text-sm text-[#6B6F85]">Belum ada aktivitas. Yuk cari layanan kampus dulu.</p>
            <a href="{{ route('catalog.index') }}" class="inline-block mt-2 text-sm font-medium" style="color:#4F46E5;">Lihat Katalog &rarr;</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[380px_1fr] gap-6 items-start">

            {{-- ================= SIDEBAR: LIST SEMUA AKTIVITAS + RIWAYAT ================= --}}
            <aside class="space-y-3">
                @foreach ($daftarAktivitas as $item)
                    @php
                        $aktif = ($item->is_pesanan && (int) $item->id_pesanan === (int) $selectedId) ||
                                 (! $item->is_pesanan && (int) $item->id_negosiasi === (int) optional($selectedNegosiasi)->id_negosiasi);
                    @endphp
                    <a href="{{ $item->url }}"
                       class="flex items-center gap-3 bg-white border rounded-2xl p-4 transition {{ $aktif ? 'border-[#4F46E5] ring-2 ring-[#4F46E5]/20' : 'border-[#14162B14] hover:shadow-sm' }}">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center font-display font-bold shrink-0" style="background:#EEF1FB; color:#4F46E5;">
                            {{ strtoupper(substr($item->nama_provider, 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ Str::limit($item->judul, 30) }}</p>
                            <p class="text-xs text-[#6B6F85] mt-0.5">{{ $item->kode }} · {{ $item->tanggal->translatedFormat('d M Y') }}</p>
                        </div>
                        <span @class([
                            'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap shrink-0',
                            'bg-[#4F46E51A] text-[#4F46E5]' => $item->badge_color === 'indigo',
                            'bg-[#F59E0B26] text-[#F59E0B]' => $item->badge_color === 'amber',
                            'bg-green-100 text-green-700' => $item->badge_color === 'green',
                            'bg-gray-100 text-gray-500' => $item->badge_color === 'gray',
                        ])>
                            {{ $item->badge }}
                        </span>
                    </a>
                @endforeach
            </aside>

            {{-- ================= DETAIL PANEL ================= --}}
            <div class="bg-white border border-[#14162B14] rounded-2xl overflow-hidden">
                @if ($selectedNegosiasi)
                    {{-- DETAIL PANEL: NEGOSIASI AKTIF --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6" style="background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);">
                        <div class="flex items-center gap-4">
                            <span class="w-16 h-16 rounded-full flex items-center justify-center font-display font-bold text-xl text-white shrink-0" style="background:rgba(255,255,255,0.2); box-shadow:0 0 0 4px rgba(255,255,255,0.3);">
                                {{ strtoupper(substr($selectedNegosiasi->provider->user->username, 0, 1)) }}
                            </span>
                            <div>
                                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide uppercase text-white" style="background:rgba(255,255,255,0.25);">
                                    Status · Tahap Negosiasi Chat
                                </span>
                                <h2 class="font-display font-bold text-xl text-white mt-1">{{ Str::limit($selectedNegosiasi->request->detail_kebutuhan, 40) }}</h2>
                                <p class="text-sm text-white/90 mt-0.5">{{ $selectedNegosiasi->provider->user->username }} · {{ $selectedNegosiasi->request->kategori }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[11px] uppercase tracking-wide text-white/80">Tawaran Terakhir</p>
                            <p class="font-display font-extrabold text-3xl text-white">
                                Rp{{ number_format($selectedNegosiasi->harga_tawaran ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="bg-amber-50/60 border border-amber-200/70 rounded-2xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs text-amber-900 leading-relaxed">
                                <p class="font-bold">Diskusi Penawaran Harga</p>
                                <p class="mt-0.5 text-amber-800">
                                    Mitra <span class="font-bold">{{ $selectedNegosiasi->provider->user->username }}</span> telah mengajukan penawaran. Kamu dapat menyetujui tawaran ini atau mengajukan tawar ulang.
                                </p>
                            </div>
                        </div>

                        <div class="bg-[#EEF1FB66] rounded-2xl p-5 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-[#6B6F85]">Kode Negosiasi</p>
                                <p class="font-semibold text-xs mt-0.5">NEG-{{ str_pad($selectedNegosiasi->id_negosiasi, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-[#6B6F85]">Tanggal Diajukan</p>
                                <p class="font-semibold text-xs mt-0.5">{{ $selectedNegosiasi->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-[#6B6F85]">Detail Kebutuhan</p>
                                <p class="font-medium text-xs text-slate-800 mt-1 bg-white p-3 rounded-xl border border-slate-200/80">
                                    {{ $selectedNegosiasi->request->detail_kebutuhan }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('negosiasi.show', $selectedNegosiasi->id_negosiasi) }}"
                           class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-amber-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Buka Ruang Chat &amp; Negosiasi Harga &rarr;
                        </a>
                    </div>
                @elseif ($selected)
                    {{-- Header gradien --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6" style="background: linear-gradient(90deg, #4F46E5 0%, #14B8A6 100%);">
                        <div class="flex items-center gap-4">
                            <span class="w-16 h-16 rounded-full flex items-center justify-center font-display font-bold text-xl text-white shrink-0" style="background:rgba(255,255,255,0.15); box-shadow:0 0 0 4px rgba(255,255,255,0.3);">
                                {{ strtoupper(substr($selected->negosiasi->provider->user->username, 0, 1)) }}
                            </span>
                            <div>
                                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide uppercase text-white" style="background:rgba(255,255,255,0.25);">
                                    Status · {{ match($selected->status_pesanan) {
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                        'dikerjakan', 'revisi', 'diproses' => 'Diproses / Dikerjakan',
                                        'menunggu_pengerjaan' => 'Dikonfirmasi (Menunggu Pengerjaan)',
                                        default => 'Diproses'
                                    } }}
                                </span>
                                <h2 class="font-display font-bold text-xl text-white mt-1">{{ Str::limit($selected->negosiasi->request->detail_kebutuhan, 40) }}</h2>
                                <p class="text-sm text-white/80 mt-0.5">{{ $selected->negosiasi->provider->user->username }} · {{ $selected->negosiasi->request->kategori }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[11px] uppercase tracking-wide text-white/70">Total</p>
                            <p class="font-display font-extrabold text-3xl text-white">Rp{{ number_format($selected->harga_final, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Progress stepper --}}
                        <div class="flex items-center">
                            @php $steps = ['Dipesan', 'Dikonfirmasi', 'Diproses', 'Selesai']; @endphp
                            @foreach ($steps as $i => $label)
                                @php $selesaiStep = $stepIndex >= $i; @endphp
                                <div class="flex flex-col items-center {{ $i < 3 ? 'flex-1' : '' }}">
                                    <span class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                          style="background: {{ $selesaiStep ? '#4F46E5' : '#EEF0F6' }};">
                                        @if ($selesaiStep)
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-9" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    </span>
                                    <span class="text-xs text-[#6B6F85] mt-1 whitespace-nowrap">{{ $label }}</span>
                                </div>
                                @if ($i < 3)
                                    <div class="flex-1 h-1 rounded-full -mt-5" style="background: {{ $stepIndex > $i ? '#4F46E5' : '#EEF0F6' }};"></div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Info grid --}}
                        <div class="mt-6 bg-[#EEF1FB66] rounded-2xl p-5 grid grid-cols-2 gap-y-4 gap-x-4">
                            <div>
                                <p class="text-sm text-[#6B6F85]">ID Pesanan</p>
                                <p class="font-semibold">ORD-{{ str_pad($selected->id_pesanan, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-[#6B6F85]">Tanggal</p>
                                <p class="font-semibold">{{ $selected->tanggal_pesanan->translatedFormat('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-[#6B6F85]">Kategori</p>
                                <p class="font-semibold">{{ $selected->negosiasi->request->kategori }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-[#6B6F85]">Penyedia</p>
                                <p class="font-display font-semibold text-lg" style="color:#4F46E5;">{{ $selected->negosiasi->provider->user->username }}</p>
                            </div>
                        </div>

                        {{-- Review / Struk --}}
                        <div class="mt-6 border border-[#14162B0E] rounded-2xl p-5">
                            @if ($selected->ratingReview)
                                <p class="font-display font-bold text-base">Ulasan Kamu</p>
                                <p class="mt-2" style="color:#F59E0B;">
                                    {{ str_repeat('★', $selected->ratingReview->rate) }}{{ str_repeat('☆', 5 - $selected->ratingReview->rate) }}
                                </p>
                                @if ($selected->ratingReview->review)
                                    <p class="text-sm text-[#6B6F85] mt-1">{{ $selected->ratingReview->review }}</p>
                                @endif
                                <div class="flex gap-2 mt-4 flex-wrap">
                                    <a href="{{ route('pesanan.struk', $selected->id_pesanan) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 3h10v6H5zM4 8h12M6 15h8" stroke="#16182B" stroke-width="1.3"/></svg>
                                        Lihat Struk
                                    </a>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold text-slate-800 bg-indigo-50 border border-indigo-200">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>Kontak In-App: {{ $selected->negosiasi->provider->user->no_hp ?? '-' }}</span>
                                    </div>
                                </div>
                            @elseif ($selected->bolehDireview())
                                <p class="font-display font-bold text-base">Beri penilaian</p>
                                <form method="POST" action="{{ route('review.store', $selected->id_pesanan) }}" class="mt-3" x-data="{ currentRate: 5 }">
                                    @csrf
                                    <input type="hidden" name="rate" value="5" :value="currentRate">
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                            <button type="button" @click="currentRate = star" class="text-3xl transition cursor-pointer"
                                                    :class="star <= currentRate ? 'text-amber-400 scale-110' : 'text-slate-200 hover:text-amber-300'">
                                                ★
                                            </button>
                                        </template>
                                        <span class="text-xs font-bold text-slate-600 ml-2" x-text="currentRate + ' Bintang'"></span>
                                    </div>
                                    <input type="text" name="review" placeholder="Ceritakan pengalamanmu (opsional)"
                                           class="w-full mt-3 border border-[#14162B14] rounded-lg px-3 py-2 text-sm">
                                    <div class="flex gap-2 mt-3">
                                        <button type="submit" class="px-5 py-2 rounded-full text-sm font-medium text-white cursor-pointer" style="background:#4F46E5;">Kirim Ulasan</button>
                                        <a href="{{ route('pesanan.struk', $selected->id_pesanan) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                                            Lihat Struk
                                        </a>
                                    </div>
                                </form>
                            @elseif ($selected->pembayaran)
                                <p class="text-sm text-[#6B6F85]">
                                    Status pembayaran: <span class="font-semibold text-[#16182B]">{{ ucfirst(str_replace('_',' ', $selected->pembayaran->status_bayar)) }}</span>
                                </p>
                                <a href="{{ route('pesanan.struk', $selected->id_pesanan) }}" target="_blank"
                                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                                    Lihat Struk
                                </a>
                            @elseif ($selected->status_pesanan === 'selesai')
                                <p class="text-sm text-[#6B6F85] mb-3">Pesanan selesai — silakan lakukan pembayaran.</p>
                                <form method="POST" action="{{ route('pembayaran.store', $selected->id_pesanan) }}" enctype="multipart/form-data" class="space-y-3 max-w-sm">
                                    @csrf
                                    <input type="text" name="metode_pembayaran" placeholder="Metode pembayaran" required class="w-full border border-[#14162B14] rounded-lg px-3 py-2 text-sm">
                                    <input type="file" name="bukti_pembayaran" required class="w-full text-sm">
                                    <button class="px-5 py-2 rounded-full text-sm font-medium text-white" style="background:#4F46E5;">Kirim Bukti Pembayaran</button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-500">
                                        Pantau log progress pengerjaan, instruksi detail, dan berkas deliverable hasil pekerjaan mitra.
                                    </p>
                                    <a href="{{ route('pesanan.show', $selected->id_pesanan) }}"
                                       class="w-full py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Buka Detail Pesanan &amp; Progress Pekerjaan &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-10 text-center text-sm text-[#6B6F85]">Pilih pesanan di sebelah kiri untuk lihat detail.</div>
                @endif
            </div>
        </div>
    @endif
@endsection
