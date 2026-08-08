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
            <p class="text-sm text-[#6B6F85]">Belum ada pesanan. Yuk cari layanan dulu.</p>
            <a href="{{ route('catalog.index') }}" class="inline-block mt-2 text-sm font-medium" style="color:#4F46E5;">Lihat Katalog &rarr;</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[380px_1fr] gap-6 items-start">

            {{-- ================= SIDEBAR: LIST SEMUA AKTIVITAS + RIWAYAT ================= --}}
            <aside class="space-y-3">
                @foreach ($daftarAktivitas as $item)
                    @php $aktif = $item->is_pesanan && (int) $item->id_pesanan === (int) $selectedId; @endphp
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
                @if ($selected)
                    {{-- Header gradien --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6" style="background: linear-gradient(90deg, #4F46E5 0%, #14B8A6 100%);">
                        <div class="flex items-center gap-4">
                            <span class="w-16 h-16 rounded-full flex items-center justify-center font-display font-bold text-xl text-white shrink-0" style="background:rgba(255,255,255,0.15); box-shadow:0 0 0 4px rgba(255,255,255,0.3);">
                                {{ strtoupper(substr($selected->negosiasi->provider->user->username, 0, 1)) }}
                            </span>
                            <div>
                                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide uppercase text-white" style="background:rgba(255,255,255,0.2);">
                                    Status · {{ $selected->status_pesanan === 'selesai' ? 'Selesai' : ($selected->status_pesanan === 'dibatalkan' ? 'Dibatalkan' : 'Diproses') }}
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
                                @php
                                    $noHpMitra = preg_replace('/^0/', '62', $selected->negosiasi->provider->user->no_hp ?? '08123456789');
                                @endphp
                                <div class="flex gap-2 mt-4 flex-wrap">
                                    <a href="{{ route('pesanan.struk', $selected->id_pesanan) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 3h10v6H5zM4 8h12M6 15h8" stroke="#16182B" stroke-width="1.3"/></svg>
                                        Lihat Struk
                                    </a>
                                    <a href="https://wa.me/{{ $noHpMitra }}?text=Halo%20{{ urlencode($selected->negosiasi->provider->user->username) }},%20saya%20pemesan%20ORD-{{ str_pad($selected->id_pesanan, 4, '0', STR_PAD_LEFT) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l.288.459-1.15 4.195 4.298-1.127.307.14z"/></svg>
                                        Chat WA Mitra
                                    </a>
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
                                <p class="text-sm text-[#6B6F85]">
                                    Lihat progres & detail lengkap pekerjaan di
                                    <a href="{{ route('pesanan.show', $selected->id_pesanan) }}" class="font-medium" style="color:#4F46E5;">halaman detail pesanan</a>.
                                </p>
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
