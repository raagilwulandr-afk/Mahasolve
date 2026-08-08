@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $pesanan->id_pesanan . ' — Mahasolve')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 py-6">

    <!-- TOP NAV BACK & BREADCRUMB -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition">
            &larr; Kembali ke Pesanan Saya
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Kode Transaksi:</span>
            <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">ORD-{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <!-- HERO ORDER CARD BANNER -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg width="300" height="300" viewBox="0 0 24 24" fill="currentColor">
                <path d="M13 3L4 14H11L11 21L20 10H13L13 3Z"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">
                        STATUS: {{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                    </span>
                    <span class="text-xs text-indigo-100">Dibuat {{ $pesanan->tanggal_pesanan?->format('d M Y') ?? 'Baru saja' }}</span>
                </div>
                <h1 class="text-2xl font-extrabold font-display leading-tight">
                    {{ $pesanan->negosiasi->request->detail_kebutuhan ?? 'Detail Pesanan Layanan' }}
                </h1>
                <p class="text-xs text-indigo-100 flex items-center gap-2">
                    <span>Mitra Provider: <strong>{{ $pesanan->negosiasi->provider->user->name ?? $pesanan->negosiasi->provider->user->username }}</strong></span>
                    <span>•</span>
                    <span>@ {{ $pesanan->negosiasi->provider->user->username }}</span>
                </p>
            </div>

            <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-right min-w-[200px]">
                <span class="text-xs text-indigo-200 block uppercase font-semibold">Total Biaya Final</span>
                <span class="text-2xl font-black font-display">Rp{{ number_format($pesanan->harga_final, 0, ',', '.') }}</span>
                @php
                    $targetUser = auth()->user()->isMahasiswa() ? $pesanan->negosiasi->provider->user : $pesanan->negosiasi->request->mahasiswa;
                    $noHpTarget = preg_replace('/^0/', '62', $targetUser->no_hp ?? '08123456789');
                @endphp
                <div class="mt-2 space-y-1.5">
                    @if ($pesanan->pembayaran && $pesanan->pembayaran->status_bayar === 'dikonfirmasi')
                        <a href="{{ route('pesanan.struk', $pesanan->id_pesanan) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 w-full py-1.5 bg-white text-indigo-700 text-xs font-bold rounded-xl hover:bg-indigo-50 transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Struk Digital
                        </a>
                    @endif
                    <a href="https://wa.me/{{ $noHpTarget }}?text=Halo%20{{ urlencode($targetUser->username) }},%20saya%20terkait%20pesanan%20ORD-{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-1.5 w-full py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l.288.459-1.15 4.195 4.298-1.127.307.14z"/></svg>
                        Chat WA {{ auth()->user()->isMahasiswa() ? 'Mitra' : 'Mahasiswa' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN GRID 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- KOLOM KIRI: DELIVERABLES & PROGRESS TRACKER (8 COLS) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- FORM UPLOAD DETAIL PEKERJAAN & BERKAS INTRUKSI (MAHASISWA) -->
            @if (auth()->user()->isMahasiswa() && in_array($pesanan->status_pesanan, ['menunggu_pengerjaan', 'dikerjakan', 'revisi']))
                <form method="POST" action="{{ route('detailPekerjaan.store', $pesanan->id_pesanan) }}" enctype="multipart/form-data"
                      class="bg-white border border-indigo-200 rounded-3xl p-6 shadow-sm space-y-4">
                    @csrf
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-sm text-slate-900">Upload Berkas &amp; Instruksi Pekerjaan</h3>
                            <p class="text-xs text-slate-500">Kirimkan instruksi detail, catatan revisi, atau file pendukung ke Mitra Provider.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Instruksi &amp; Detail Catatan Pekerjaan <span class="text-rose-500">*</span></label>
                        <textarea name="instruksi_pengerjaan" rows="3" required placeholder="Jelaskan kebutuhan pengerjaan, poin revisi, atau catatan khusus..."
                                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Berkas / File Utama (Max 10MB)</label>
                            <input type="file" name="dokumen" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Upload File Referensi Tambahan (Opsional)</label>
                            <input type="file" name="referensi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs rounded-2xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Kirim Detail Pekerjaan &amp; Berkas ke Provider
                    </button>
                </form>
            @endif

            <!-- DELIVERABLES & HASIL PEKERJAAN -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Berkas Deliverable &amp; Hasil Pekerjaan
                        </h2>
                        <p class="text-xs text-slate-500">File atau tautan dokumen yang diserahkan oleh provider.</p>
                    </div>
                </div>

                @forelse ($pesanan->detailPekerjaan as $detail)
                    <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-2">
                        <div class="flex items-start justify-between">
                            <p class="text-xs font-semibold text-slate-800">{{ $detail->instruksi_pengerjaan }}</p>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $detail->tanggal_upload?->format('d M Y H:i') }}</span>
                        </div>
                        @if ($detail->dokumen)
                            <div class="pt-2 flex items-center gap-2">
                                <a href="{{ Str::startsWith($detail->dokumen, 'http') ? $detail->dokumen : Storage::url($detail->dokumen) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Buka Dokumen Deliverable
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-xs text-slate-400">Belum ada file deliverable yang dikirimkan provider.</p>
                    </div>
                @endforelse
            </div>

            <!-- RIWAYAT PROGRESS TRACKER -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z"/>
                    </svg>
                    Log Riwayat Progress Pengerjaan
                </h2>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse ($pesanan->trackingPesanan as $track)
                        <div class="relative group">
                            <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-indigo-600 ring-4 ring-white"></div>
                            <div class="bg-slate-50 border border-slate-100 p-3.5 rounded-2xl space-y-1">
                                <p class="text-xs font-bold text-slate-800">{{ $track->status_pengerjaan }}</p>
                                <p class="text-[11px] text-slate-400">{{ $track->created_at?->format('d M Y H:i') }}</p>
                                @if ($track->file_progress)
                                    <a href="{{ Str::startsWith($track->file_progress, 'http') ? $track->file_progress : Storage::url($track->file_progress) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline block pt-1">
                                        Berkas / Link Tambahan
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada catatan log progress.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: PEMBAYARAN & RATING (4 COLS) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- STATISTIK PEMBAYARAN -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status Pembayaran</h3>

                @if ($pesanan->pembayaran)
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Metode:</span>
                            <span class="font-bold text-slate-800">{{ $pesanan->pembayaran->metode_pembayaran }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Status Bayar:</span>
                            <span class="font-bold text-emerald-700 capitalize">{{ str_replace('_', ' ', $pesanan->pembayaran->status_bayar) }}</span>
                        </div>
                        <div class="pt-2 border-t border-emerald-200/60 flex justify-between items-center text-xs">
                            <span class="font-bold text-emerald-800">Total Dibayar:</span>
                            <span class="font-bold text-emerald-800">Rp{{ number_format($pesanan->pembayaran->total_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <!-- SIMULASI BAYAR QRIS MODAL POPUP -->
                    <div x-data="{ showQrisModal: false }">
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-center space-y-2">
                            <svg class="w-8 h-8 text-amber-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <h4 class="font-bold text-slate-800 text-xs">Pembayaran Belum Dikonfirmasi</h4>
                            <p class="text-[11px] text-slate-500">Lakukan simulasi pembayaran QRIS Unikom Pay untuk menyelesaikan pesanan.</p>
                        </div>
                        <button type="button" @click="showQrisModal = true" class="w-full mt-3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Bayar via QRIS Unikom Pay
                        </button>

                        {{-- MODAL POPUP QRIS --}}
                        <div x-show="showQrisModal" x-transition class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display:none;">
                            <div @click.away="showQrisModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full shadow-2xl space-y-5 text-center">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <img src="{{ asset('images/qris-logo.png') }}" alt="Official QRIS Logo" class="h-9 object-contain">
                                    <button type="button" @click="showQrisModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer px-2">✕</button>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Total Pembayaran</p>
                                    <p class="font-display font-black text-2xl text-indigo-600">Rp{{ number_format($pesanan->harga_final, 0, ',', '.') }}</p>
                                    <p class="text-[11px] text-slate-500">ORD-{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>

                                <!-- VISUAL QR CODE CONTAINER -->
                                <div class="bg-slate-50 border-2 border-dashed border-indigo-200 rounded-2xl p-6 relative flex flex-col items-center justify-center">
                                    <div class="w-44 h-44 bg-white p-3 rounded-xl border border-slate-200 shadow-inner flex items-center justify-center">
                                        <svg class="w-full h-full text-slate-800" viewBox="0 0 100 100" fill="currentColor">
                                            <path d="M10,10 h30 v30 h-30 z M15,15 v20 h20 v-20 z M22,22 h6 v6 h-6 z"/>
                                            <path d="M60,10 h30 v30 h-30 z M65,15 v20 h20 v-20 z M72,22 h6 v6 h-6 z"/>
                                            <path d="M10,60 h30 v30 h-30 z M15,65 v20 h20 v-20 z M22,72 h6 v6 h-6 z"/>
                                            <path d="M50,10 h5 v15 h-5 z M45,35 h15 v5 h-15 z M50,50 h10 v10 h-10 z M65,50 h25 v5 h-25 z M80,60 h10 v25 h-10 z M50,75 h20 v15 h-20 z"/>
                                        </svg>
                                    </div>
                                    <span class="mt-3 inline-block px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">Batas Bayar: 14:59 min</span>
                                </div>

                                <form method="POST" action="{{ route('pembayaran.store', $pesanan->id_pesanan) }}">
                                    @csrf
                                    <input type="hidden" name="metode_pembayaran" value="QRIS Unikom Pay">
                                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow-md shadow-emerald-500/20 transition cursor-pointer">
                                        Simulasi Scan &amp; Bayar Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- RATING & REVIEW -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ulasan &amp; Rating</h3>

                @if ($pesanan->ratingReview)
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-2 text-center">
                        <div class="flex items-center justify-center gap-1 text-amber-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $pesanan->ratingReview->rate ? 'fill-current text-amber-400' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs text-slate-700 italic">"{{ $pesanan->ratingReview->review }}"</p>
                    </div>
                @elseif ($pesanan->bolehDireview())
                    <form method="POST" action="{{ route('review.store', $pesanan->id_pesanan) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Berikan Bintang</label>
                            <select name="rate" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs bg-white focus:outline-none focus:border-indigo-500">
                                <option value="5">Bintang 5 (Sangat Puas)</option>
                                <option value="4">Bintang 4 (Puas)</option>
                                <option value="3">Bintang 3 (Cukup)</option>
                                <option value="2">Bintang 2 (Kurang)</option>
                                <option value="1">Bintang 1 (Kecewa)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tuliskan Ulasan</label>
                            <textarea name="review" rows="2" placeholder="Ceritakan pengalaman kamu..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition">
                            Kirim Ulasan &amp; Rating
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-slate-50 rounded-2xl text-center text-xs text-slate-400">
                        Review dapat diberikan setelah pesanan selesai &amp; dikonfirmasi.
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
