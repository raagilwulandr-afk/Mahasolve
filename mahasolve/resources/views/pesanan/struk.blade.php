<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk — ORD-{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } .font-display { font-family: 'Montserrat', sans-serif; } </style>
</head>
<body class="bg-[#F7F8FC] text-[#16182B] min-h-screen py-10 print:py-0 print:bg-white">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Banner sukses --}}
        @if ($pesanan->pembayaran && $pesanan->pembayaran->status_bayar === 'dikonfirmasi')
            <div class="flex items-center gap-4 bg-green-50 border border-green-200 rounded-2xl p-5 print:hidden">
                <span class="w-12 h-12 rounded-full flex items-center justify-center shrink-0" style="background:#16A34A;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 13l5 5L20 6" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <p class="font-display font-bold text-green-700">Pembayaran Berhasil</p>
                    <p class="text-sm text-[#6B6F85]">Struk pembayaran ini bisa kamu simpan sebagai bukti transaksi.</p>
                </div>
            </div>
        @endif

        {{-- Kartu struk utama --}}
        <div class="bg-white border border-[#14162B14] rounded-2xl overflow-hidden mt-6 print:mt-0 print:border-0">
            <div class="flex items-center justify-between p-6 bg-[#EEF1FB66]">
                <div>
                    <h1 class="font-display font-extrabold text-2xl">Struk Pembayaran</h1>
                    <p class="text-sm text-[#6B6F85] mt-1">No. INV/{{ $pesanan->tanggal_pesanan->format('Y/m') }}/{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/qris-logo.png') }}" alt="Official QRIS Logo" class="h-7 object-contain">
                    <div class="flex items-center gap-1.5">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M2 12l6-8 4 5 6-7" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-display font-extrabold text-base">Mahasolve</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Detail grid --}}
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm text-[#6B6F85]">Detail Mahasiswa</p>
                        <p class="font-semibold mt-1">{{ auth()->user()->username }}</p>
                        <p class="text-sm text-[#6B6F85]">{{ auth()->user()->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#6B6F85]">Tanggal Pembayaran</p>
                        <p class="font-semibold mt-1">{{ $pesanan->tanggal_pesanan->translatedFormat('d F Y') }}</p>
                        <p class="text-sm text-[#6B6F85]">{{ $pesanan->tanggal_pesanan->format('H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#6B6F85]">Penyedia Jasa</p>
                        <p class="font-semibold mt-1">{{ $pesanan->negosiasi->provider->user->username }}</p>
                        <p class="text-sm text-[#6B6F85]">{{ $pesanan->negosiasi->request->kategori }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#6B6F85]">Metode Pembayaran</p>
                        <p class="font-semibold mt-1">{{ $pesanan->pembayaran->metode_pembayaran ?? '-' }}</p>
                        @if ($pesanan->pembayaran)
                            <span @class([
                                'inline-block mt-1 text-xs px-2 py-0.5 rounded-full font-medium',
                                'bg-green-100 text-green-700' => $pesanan->pembayaran->status_bayar === 'dikonfirmasi',
                                'bg-yellow-100 text-yellow-700' => $pesanan->pembayaran->status_bayar === 'menunggu_konfirmasi',
                                'bg-red-100 text-red-700' => $pesanan->pembayaran->status_bayar === 'ditolak',
                            ])>
                                {{ ucfirst(str_replace('_',' ', $pesanan->pembayaran->status_bayar)) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="border-t border-[#14162B14] my-6"></div>

                {{-- Rincian layanan --}}
                <div class="flex justify-between text-sm font-semibold text-[#6B6F85] pb-2">
                    <span>Deskripsi Layanan</span>
                    <span>Subtotal</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <p class="text-sm">{{ Str::limit($pesanan->negosiasi->request->detail_kebutuhan, 50) }}</p>
                    <p class="font-medium">Rp{{ number_format($pesanan->harga_final, 0, ',', '.') }}</p>
                </div>

                <div class="border-t border-[#14162B14] my-6"></div>

                {{-- Total --}}
                <div class="flex justify-between items-center">
                    <span class="font-display font-bold text-lg">Total</span>
                    <span class="font-display font-extrabold text-2xl" style="color:#4F46E5;">Rp{{ number_format($pesanan->harga_final, 0, ',', '.') }}</span>
                </div>

                {{-- ID transaksi --}}
                <div class="mt-6 bg-[#EEF1FB66] rounded-2xl p-4 flex items-center gap-4">
                    <span class="w-16 h-16 rounded-xl bg-white flex items-center justify-center shrink-0">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="6" height="6" fill="#16182B"/><rect x="15" y="3" width="6" height="6" fill="#16182B"/><rect x="3" y="15" width="6" height="6" fill="#16182B"/><rect x="12" y="3" width="2" height="2" fill="#16182B"/><rect x="15" y="12" width="2" height="2" fill="#16182B"/><rect x="18" y="15" width="3" height="3" fill="#16182B"/><rect x="12" y="18" width="2" height="2" fill="#16182B"/></svg>
                    </span>
                    <div>
                        <p class="font-semibold text-sm">ID Transaksi</p>
                        <p class="text-sm text-[#6B6F85] mt-0.5">ORD-{{ str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) }} — struk digital Mahasolve, simpan sebagai bukti transaksi.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Beri rating (kalau belum) --}}
        @if ($pesanan->status_pesanan === 'selesai' && !$pesanan->ratingReview && $pesanan->bolehDireview())
            <div class="bg-white border border-[#14162B14] rounded-2xl p-6 mt-6 print:hidden">
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-full flex items-center justify-center font-display font-bold shrink-0" style="background:#EEF1FB; color:#4F46E5;">
                        {{ strtoupper(substr($pesanan->negosiasi->provider->user->username, 0, 1)) }}
                    </span>
                    <div>
                        <p class="font-display font-bold">Beri Rating Penyedia Jasa</p>
                        <p class="text-sm text-[#6B6F85]">Bagaimana pengalamanmu dengan {{ $pesanan->negosiasi->provider->user->username }}?</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('review.store', $pesanan->id_pesanan) }}" class="mt-4">
                    @csrf
                    <div class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rate" value="{{ $i }}" class="hidden peer" required>
                                <svg class="w-8 h-8 text-[#EEF0F6] peer-checked:text-[#F59E0B] fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>
                    <textarea name="review" rows="3" placeholder="Tulis ulasan singkat (opsional)..."
                              class="w-full mt-3 border-0 bg-[#F1F3FA] rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/30"></textarea>
                    <button class="mt-3 px-5 py-2 rounded-full text-sm font-medium text-white" style="background:#4F46E5;">Kirim Ulasan</button>
                </form>
            </div>
        @endif

        {{-- Aksi bawah --}}
        <div class="flex flex-wrap gap-3 mt-6 print:hidden">
            <button onclick="window.print()" class="px-6 py-2.5 rounded-full text-sm font-medium text-white" style="background:#4F46E5;">
                Cetak Struk
            </button>
            <button onclick="navigator.share ? navigator.share({title: 'Struk Mahasolve', url: window.location.href}) : (navigator.clipboard.writeText(window.location.href), alert('Link struk disalin ke clipboard'))"
                    class="px-6 py-2.5 rounded-full text-sm font-medium border border-[#14162B14] bg-white hover:bg-[#F7F8FC] transition">
                Bagikan
            </button>
            <a href="{{ route('pesanan.index', ['pesanan' => $pesanan->id_pesanan]) }}"
               class="px-6 py-2.5 rounded-full text-sm font-medium border border-[#14162B14] bg-white hover:bg-[#F7F8FC] transition">
                Ke Pesanan
            </a>
        </div>
    </div>
</body>
</html>
