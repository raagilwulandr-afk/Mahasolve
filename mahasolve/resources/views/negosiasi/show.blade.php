@extends('layouts.app')

@section('title', 'Negosiasi — ' . $negosiasi->provider->user->username)

@section('content')
    <div x-data="{ showRejectModal: false }" class="grid lg:grid-cols-[300px_1fr] gap-6 items-start">

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
                    <span class="font-semibold flex items-center gap-1">
                        {{ number_format($negosiasi->provider->rating, 1) }}
                        <svg class="w-4 h-4 text-amber-400 fill-current inline" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </span>
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
                                        <button type="button" @click="showRejectModal = true"
                                                class="px-3.5 py-1.5 rounded-full text-xs font-bold text-rose-600 border border-rose-200 bg-rose-50 hover:bg-rose-100 transition cursor-pointer flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Tolak Negosiasi
                                        </button>
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
                                        <button type="button" @click="showRejectModal = true"
                                                class="px-4 py-2 rounded-full text-sm font-bold border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 transition cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Tolak Negosiasi
                                        </button>
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
                <form id="counter-form" onsubmit="event.preventDefault(); sendMahasiswaChat(this);" method="POST" action="{{ route('negosiasi.counter', $negosiasi->id_negosiasi) }}"
                      class="border-t border-[#14162B0E] p-4 {{ $terakhir->dibuat_oleh === 'mahasiswa' ? 'hidden' : '' }}">
                    @csrf
                    <div class="flex gap-2">
                        <input type="number" id="input_harga_tawaran" name="harga_tawaran" min="0" required placeholder="Harga (Rp)" autocomplete="off"
                               value="{{ $terakhir->harga_tawaran }}"
                               class="w-32 bg-[#EEF1FB80] rounded-full px-4 py-2.5 text-sm focus:outline-none">
                        <input type="text" id="input_detail_negosiasi" name="detail_negosiasi" placeholder="Tulis pesan..." autocomplete="off"
                               class="flex-1 bg-[#EEF1FB80] rounded-full px-4 py-2.5 text-sm focus:outline-none">
                        <button type="submit" class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 cursor-pointer" style="background:#4F46E5;">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M17 3L2 9l6 2 2 6 7-14z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </form>

                <script>
                    async function sendMahasiswaChat(form) {
                        const harga = document.getElementById('input_harga_tawaran').value;
                        const detail = document.getElementById('input_detail_negosiasi').value;
                        if (!harga) return;

                        const now = new Date();
                        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                        const chatContainer = document.getElementById('chat-container');
                        const bubble = document.createElement('div');
                        bubble.className = 'flex justify-end';
                        bubble.innerHTML = `
                            <div class="max-w-md bg-[#4F46E5] text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm">${detail || ('Menawarkan Rp' + parseInt(harga).toLocaleString('id-ID'))}</p>
                                <p class="text-[10px] mt-1 text-right text-white/70">${timeStr}</p>
                            </div>
                        `;
                        if (chatContainer) {
                            chatContainer.appendChild(bubble);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }

                        form.style.display = 'none';

                        try {
                            const formData = new FormData(form);
                            await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });
                        } catch (err) {
                            console.error(err);
                        }
                    }
                </script>
            @endif
        <!-- MODERN CUSTOM GLASSMORPHISM REJECT CONFIRMATION MODAL -->
        <template x-teleport="body">
            <div x-show="showRejectModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4"
                 style="display: none;">
                <div @click.away="showRejectModal = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 border border-slate-100">
                    
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-extrabold text-lg text-slate-900">Tolak &amp; Batalkan Negosiasi?</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Konfirmasi pembatalan penawaran harga</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        Apakah Anda yakin ingin menolak penawaran harga ini? Negosiasi akan dibatalkan dan status permintaan Anda akan dibuka kembali agar bisa memilih mitra lain.
                    </p>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showRejectModal = false"
                                class="px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('negosiasi.reject', $negosiasi->id_negosiasi) }}">
                            @csrf
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-500/20 cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Ya, Tolak Negosiasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
