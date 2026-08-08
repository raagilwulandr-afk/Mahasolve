@extends('layouts.app')

@section('title', 'Dashboard Provider — Mahasolve')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 font-display">Dashboard Penyedia Jasa</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola pesanan, negosiasi, dan pendapatanmu.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('my-service') }}" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs rounded-2xl transition shadow-md shadow-indigo-500/20 flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Kelola Layanan
            </a>
            <a href="{{ route('order') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs rounded-2xl transition shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Order Masuk
            </a>
        </div>
    </div>

    <!-- STATISTIK (4 STAT CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Pendapatan + Withdrawal Modal -->
        <div x-data="{ showWithdrawModal: false }" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <button type="button" @click="showWithdrawModal = true" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-full transition cursor-pointer shrink-0 border border-indigo-100 shadow-2xs">
                    Cairkan Saldo
                </button>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-900 font-display">Rp{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-400 font-medium">Pendapatan bulan ini</div>
            </div>

            {{-- MODAL CAIRKAN SALDO --}}
            <template x-teleport="body">
                <div x-show="showWithdrawModal" x-transition class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[9999] flex items-center justify-center p-4" style="display:none;">
                    <div @click.away="showWithdrawModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 text-left">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="font-display font-extrabold text-base text-slate-900">Penarikan Saldo Pendapatan</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Transfer saldo pendapatan mitra ke E-Wallet atau Rekening Bank.</p>
                            </div>
                            <button type="button" @click="showWithdrawModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('provider.withdraw') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan Pencairan (E-Wallet / Bank)</label>
                                <select name="metode" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">
                                    <option value="GoPay">GoPay</option>
                                    <option value="OVO">OVO</option>
                                    <option value="DANA">DANA</option>
                                    <option value="ShopeePay">ShopeePay</option>
                                    <option value="Bank BCA">Bank BCA</option>
                                    <option value="Bank Mandiri">Bank Mandiri</option>
                                </select>
                                <div class="flex items-center justify-center gap-2 pt-2.5">
                                    <img src="{{ asset('images/gopay.svg') }}" alt="GoPay" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                    <img src="{{ asset('images/ovo.svg') }}" alt="OVO" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                    <img src="{{ asset('images/dana.svg') }}" alt="DANA" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                    <img src="{{ asset('images/shopeepay.svg') }}" alt="ShopeePay" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                    <img src="{{ asset('images/bca.svg') }}" alt="BCA" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                    <img src="{{ asset('images/mandiri.svg') }}" alt="Mandiri" class="h-4 object-contain opacity-80 hover:opacity-100 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening / No HP E-Wallet</label>
                                <input type="text" name="no_rekening" placeholder="Contoh: 081234567890 / 8930129381" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Penarikan (Rp)</label>
                                <input type="number" name="jumlah" value="{{ max(10000, $totalPendapatan ?? 0) }}" min="10000" max="{{ max(10000, $totalPendapatan ?? 0) }}" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">
                                <p class="text-[10px] text-slate-400 mt-1">Minimal penarikan Rp10.000 (Bebas biaya admin).</p>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition cursor-pointer">
                                    Konfirmasi Penarikan Saldo
                                </button>
                                <button type="button" @click="showWithdrawModal = false" class="px-4 py-3 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs rounded-xl transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        <!-- Card 2: Pesanan Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <div class="text-lg font-bold text-slate-900 font-display">{{ $pesananAktif ?? 0 }}</div>
                <div class="text-xs text-slate-400">Pesanan aktif</div>
            </div>
        </div>

        <!-- Card 3: Menunggu Nego / Pesanan Baru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-lg font-bold text-slate-900 font-display">{{ $pesananBaru ?? 0 }}</div>
                <div class="text-xs text-slate-400">Menunggu nego</div>
            </div>
        </div>

        <!-- Card 4: Rating -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div>
                <div class="text-lg font-bold text-slate-900 font-display flex items-center gap-1">
                    {{ number_format($rating ?? 0, 1) }}
                    <svg class="w-4 h-4 fill-current text-amber-400" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="text-xs text-slate-400">Rating</div>
            </div>
        </div>
    </div>

    <!-- MAIN LAYOUT 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- KOLOM KIRI: PERMINTAAN BARU (7 COLS) -->
        <section class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 font-display">Permintaan Baru</h2>
                    <p class="text-xs text-slate-500">Pesanan yang menunggu respons kamu.</p>
                </div>
                <a href="{{ route('provider.requests.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($permintaanBaru as $matching)
                @php
                $requestLayanan = $matching->requestLayanan ?? null;
                $mahasiswa = $requestLayanan?->mahasiswa ?? null;
                @endphp

                <article class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between gap-4 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($mahasiswa?->nama ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">
                                {{ $mahasiswa?->nama ?? 'Mahasiswa' }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ $requestLayanan?->judul_request ?? 'Permintaan Layanan' }}
                            </p>
                            <span class="text-[10px] text-slate-400">
                                {{ $matching->tanggal_matching?->diffForHumans() ?? 'Baru saja' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right mr-1">
                            <span class="text-[10px] text-slate-400 block font-semibold">Budget</span>
                            <span class="font-bold text-sm text-indigo-600 font-display">
                                Rp{{ number_format($requestLayanan?->harga_awal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Nego --}}
                            <a href="{{ route('order', ['active' => $matching->id_negosiasi ?? $matching->id ?? 1]) }}"
                                class="px-3.5 py-1.5 border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Nego
                            </a>

                            {{-- Form & Tombol Terima --}}
                            <form method="POST" action="{{ route('order.accept', $matching->id_negosiasi ?? $matching->id ?? 1) }}">
                                @csrf
                                <button type="submit"
                                    class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer">
                                    Terima
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                    <p class="font-semibold text-slate-700">Belum ada permintaan baru.</p>
                    <p class="mt-1 text-xs text-slate-400">Permintaan mahasiswa akan muncul di sini.</p>
                </div>
                @endforelse
            </div>
        </section>

        <!-- KOLOM KANAN: LAYANAN AKTIF (5 COLS) -->
        <section class="lg:col-span-5 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 font-display">Layanan Aktif</h2>

            @if ($layananAktif && (isset($layananAktif->id_pesanan) || isset($layananAktif->id)))
            @php
                $mhsUser = $layananAktif->negosiasi?->request?->mahasiswa;
                $reqDetail = $layananAktif->negosiasi?->request?->detail_kebutuhan ?? 'Jasa Aktif';
                $hargaFinal = $layananAktif->harga_final ?? 0;
                $rawTracking = $layananAktif->trackingPesanan?->first()?->status_pengerjaan ?? 'Sedang diproses / Dikerjakan';
                $trackingText = str_replace('Provider ', 'Anda ', $rawTracking);
            @endphp
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($mhsUser?->username ?? $mhsUser?->nama ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">
                                {{ $mhsUser?->nama ?? $mhsUser?->username ?? 'Mahasiswa' }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-1">
                                {{ $reqDetail }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                        {{ ucfirst(str_replace('_', ' ', $layananAktif->status_pesanan ?? 'Diproses')) }}
                    </span>
                </div>

                <!-- Progress & Status -->
                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z"/>
                            </svg>
                            {{ $trackingText }}
                        </span>
                        <span class="font-bold text-sm text-indigo-600 font-display">
                            Rp{{ number_format($hargaFinal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                            style="width: {{ match ($layananAktif->status_pesanan ?? '') {
                                    'menunggu_pengerjaan' => '35%',
                                    'dikerjakan' => '65%',
                                    'revisi' => '85%',
                                    'selesai' => '100%',
                                    default => '50%'
                                } }}">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-1 flex gap-2">
                    <a href="{{ route('order', ['active' => $layananAktif->id_negosiasi]) }}"
                       class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition text-center shadow-sm">
                        Kelola Order &amp; Kirim Deliverable
                    </a>
                </div>
            </div>
            @else
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center space-y-2">
                <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="font-semibold text-xs text-slate-700">Belum Ada Layanan Aktif</p>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Pesanan yang disepakati dan sedang dikerjakan akan muncul di sini.</p>
            </div>
            @endif
        </section>

    </div>

</div>
@endsection