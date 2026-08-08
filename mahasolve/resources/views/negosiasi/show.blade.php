@extends('layouts.app')

@section('title', 'Negosiasi — ' . $negosiasi->provider->user->username)

@section('content')
    <div class="grid lg:grid-cols-[300px_1fr] gap-6 items-start">

        {{-- ================= SIDEBAR: DETAIL LAYANAN ================= --}}
        <aside class="bg-white border border-[#14162B14] rounded-2xl p-5 lg:sticky lg:top-24">
            <p class="text-sm font-semibold text-[#6B6F85] mb-3">Detail Layanan</p>

            <div class="bg-[#EEF1FB] rounded-2xl p-4">
                <p class="font-display font-bold text-base">{{ $layananTerkait->nama_layanan ?? $negosiasi->request->kategori }}</p>
                <p class="text-sm text-[#6B6F85] mt-1">{{ $layananTerkait->deskripsi ?? $negosiasi->request->detail_kebutuhan }}</p>
            </div>

            <div class="mt-3 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B6F85]">Harga mulai</span>
                    <span class="font-semibold">Rp{{ number_format($layananTerkait->harga ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B6F85]">Rating</span>
                    <span class="font-semibold">{{ number_format($negosiasi->provider->rating, 1) }} ⭐</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B6F85]">Order selesai</span>
                    <span class="font-semibold">{{ \App\Models\Pesanan::whereHas('negosiasi', fn($q) => $q->where('id_provider', $negosiasi->provider->id_provider))->where('status_pesanan', 'selesai')->count() }}</span>
                </div>
            </div>

            <a href="{{ route('catalog.index') }}"
               class="block mt-4 text-center px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC] hover:bg-white transition">
                Lihat penyedia lain
            </a>
        </aside>

        {{-- ================= CHAT PANEL ================= --}}
        <div class="bg-white border border-[#14162B14] rounded-2xl flex flex-col h-[calc(100vh-140px)] min-h-[500px]">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-4 py-3 border-b border-[#14162B0E]">
                <div class="relative shrink-0">
                    <span class="w-11 h-11 rounded-full flex items-center justify-center font-display font-bold" style="background:#EEF1FB; color:#4F46E5;">
                        {{ strtoupper(substr($negosiasi->provider->user->username, 0, 1)) }}
                    </span>
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white" style="background:#16A34A;"></span>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm">{{ $negosiasi->provider->user->username }}</p>
                    <p class="text-xs" style="color:#16A34A;">Online sekarang</p>
                </div>
                <span @class([
                    'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                    'bg-[#F59E0B26] text-[#F59E0B]' => in_array($terakhir->status_negosiasi, ['pending', 'ditawar_ulang']),
                    'bg-green-100 text-green-700' => $terakhir->status_negosiasi === 'disepakati',
                    'bg-red-100 text-red-700' => $terakhir->status_negosiasi === 'ditolak',
                ])>
                    {{ ucfirst(str_replace('_', ' ', $terakhir->status_negosiasi)) }}
                </span>
            </div>

            {{-- Riwayat pesan / tawaran --}}
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4" style="background: rgba(238,241,251,0.3);">
                @foreach ($thread as $pesan)
                    @php $milikMahasiswa = $pesan->dibuat_oleh === 'mahasiswa'; @endphp

                    @if ($pesan->id_negosiasi === $terakhir->id_negosiasi && $terakhir->status_negosiasi !== 'disepakati')
                        {{-- ===== Kartu Penawaran Harga (tawaran aktif) ===== --}}
                        <div class="flex {{ $milikMahasiswa ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-md w-full bg-white border border-[#4F46E54D] rounded-2xl p-4">
                                <div class="flex items-center gap-2">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 10l3-3 4 4 4-5 3 3" stroke="#4F46E5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="font-display font-bold text-sm" style="color:#4F46E5;">Penawaran Harga</p>
                                </div>

                                <div class="bg-[#EEF1FB] rounded-xl p-3 mt-3">
                                    <p class="text-sm text-[#6B6F85]">{{ $pesan->detail_negosiasi ?? $negosiasi->request->detail_kebutuhan }}</p>
                                    <p class="font-display font-extrabold text-2xl mt-1" style="color:#4F46E5;">Rp{{ number_format($pesan->harga_tawaran, 0, ',', '.') }}</p>
                                </div>

                                @if ($milikMahasiswa)
                                    <div class="flex items-center justify-between gap-3 mt-3 pt-3 border-t border-slate-100">
                                        <p class="text-xs text-[#6B6F85]">Menunggu respon provider...</p>
                                        <form method="POST" action="{{ route('negosiasi.reject', $negosiasi->id_negosiasi) }}"
                                              onsubmit="return confirm('Tolak & batalkan negosiasi ini?')">
                                            @csrf
                                            <button class="px-3.5 py-1.5 rounded-full text-xs font-bold text-rose-600 border border-rose-200 bg-rose-50 hover:bg-rose-100 transition cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Tolak Negosiasi
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex gap-2 mt-3">
                                        <form method="POST" action="{{ route('negosiasi.accept', $negosiasi->id_negosiasi) }}" class="flex-1">
                                            @csrf
                                            <button class="w-full px-4 py-2 rounded-full text-sm font-medium text-white" style="background:#4F46E5;">Terima & Bayar</button>
                                        </form>
                                        <button type="button" onclick="document.getElementById('counter-form').classList.remove('hidden'); document.getElementById('counter-form').scrollIntoView({behavior:'smooth'});"
                                                class="px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14]" style="background:#F59E0B;">
                                            Nego
                                        </button>
                                        <form method="POST" action="{{ route('negosiasi.reject', $negosiasi->id_negosiasi) }}"
                                              onsubmit="return confirm('Tolak & batalkan negosiasi ini?')">
                                            @csrf
                                            <button class="px-4 py-2 rounded-full text-sm font-medium border border-[#14162B14] bg-[#F7F8FC]">Tolak</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- ===== Bubble chat biasa ===== --}}
                        <div class="flex {{ $milikMahasiswa ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-md {{ $milikMahasiswa ? 'bg-[#4F46E5] text-white rounded-2xl rounded-br-md' : 'bg-white text-[#16182B] rounded-2xl rounded-bl-md shadow-sm' }} px-4 py-2.5">
                                <p class="text-sm">{{ $pesan->detail_negosiasi ?? 'Menawarkan Rp' . number_format($pesan->harga_tawaran, 0, ',', '.') }}</p>
                                <p class="text-[10px] mt-1 text-right {{ $milikMahasiswa ? 'text-white/70' : 'text-[#6B6F85]' }}">
                                    {{ $pesan->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if ($terakhir->status_negosiasi === 'disepakati')
                    <div class="text-center">
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700">
                            ✓ Kesepakatan tercapai — Rp{{ number_format($terakhir->harga_tawaran, 0, ',', '.') }}
                        </span>
                        <a href="{{ route('pesanan.show', $terakhir->pesanan->id_pesanan) }}" class="block mt-2 text-sm font-medium" style="color:#4F46E5;">Lihat detail pesanan &rarr;</a>
                    </div>
                @endif
            </div>

            {{-- Form kirim pesan/tawaran baru --}}
            @if ($terakhir->status_negosiasi !== 'disepakati' && $terakhir->status_negosiasi !== 'ditolak')
                <form id="counter-form" method="POST" action="{{ route('negosiasi.counter', $negosiasi->id_negosiasi) }}"
                      class="border-t border-[#14162B0E] p-4 {{ $terakhir->dibuat_oleh === 'mahasiswa' ? 'hidden' : '' }}">
                    @csrf
                    <div class="flex gap-2">
                        <input type="number" name="harga_tawaran" min="0" required placeholder="Harga (Rp)"
                               value="{{ $terakhir->harga_tawaran }}"
                               class="w-32 bg-[#EEF1FB80] rounded-full px-4 py-2.5 text-sm focus:outline-none">
                        <input type="text" name="detail_negosiasi" placeholder="Tulis pesan..."
                               class="flex-1 bg-[#EEF1FB80] rounded-full px-4 py-2.5 text-sm focus:outline-none">
                        <button class="w-11 h-11 rounded-full flex items-center justify-center shrink-0" style="background:#4F46E5;">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M17 3L2 9l6 2 2 6 7-14z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
