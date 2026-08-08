@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="bg-white border rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Pesanan #{{ $pesanan->id_pesanan }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Provider: {{ $pesanan->negosiasi->provider->user->username }}
                </p>
                <p class="text-sm text-gray-500">{{ $pesanan->negosiasi->request->detail_kebutuhan }}</p>
            </div>
            <span @class([
                'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                'bg-blue-100 text-blue-700' => $pesanan->status_pesanan === 'menunggu_pengerjaan',
                'bg-yellow-100 text-yellow-700' => $pesanan->status_pesanan === 'dikerjakan',
                'bg-orange-100 text-orange-700' => $pesanan->status_pesanan === 'revisi',
                'bg-green-100 text-green-700' => $pesanan->status_pesanan === 'selesai',
                'bg-gray-100 text-gray-500' => $pesanan->status_pesanan === 'dibatalkan',
            ])>
                {{ ucfirst(str_replace('_', ' ', $pesanan->status_pesanan)) }}
            </span>
        </div>

        <p class="text-sm font-medium text-gray-700 mt-3">Harga Final: Rp {{ number_format($pesanan->harga_final, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Status pengerjaan diperbarui oleh provider dari sisi mereka.</p>
    </div>

    {{-- ================= DETAIL PEKERJAAN (PB-06) — mahasiswa serahkan ================= --}}
    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-600 mb-3">Detail Pekerjaan</h2>

        @forelse ($pesanan->detailPekerjaan as $detail)
            <div class="border rounded-md p-3 mb-2 text-sm">
                <p class="text-gray-700">{{ $detail->instruksi_pengerjaan }}</p>
                <p class="text-gray-400 text-xs mt-1">
                    Format: {{ $detail->format_hasil ?? '-' }} ·
                    Diupload: {{ $detail->tanggal_upload->format('d M Y H:i') }}
                </p>
                @if ($detail->dokumen)
                    <a href="{{ Storage::url($detail->dokumen) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Lihat Dokumen</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400 mb-3">Belum ada detail pekerjaan yang diserahkan.</p>
        @endforelse

        @if ($pesanan->status_pesanan === 'menunggu_pengerjaan')
            <form method="POST" action="{{ route('detailPekerjaan.store', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="mt-4 space-y-3 max-w-md">
                @csrf
                <textarea name="instruksi_pengerjaan" rows="3" placeholder="Instruksi pengerjaan" required class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                <input type="text" name="referensi" placeholder="Link referensi (opsional)" class="w-full border rounded-md px-3 py-2 text-sm">
                <input type="text" name="format_hasil" placeholder="Format hasil (mis. PDF, DOCX)" class="w-full border rounded-md px-3 py-2 text-sm">
                <input type="file" name="dokumen" class="w-full text-sm">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Serahkan Detail Pekerjaan</button>
            </form>
        @endif
    </div>

    {{-- ================= TRACKING / PROGRES (PB-07) — read only, diisi provider ================= --}}
    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-600 mb-3">Progres Pengerjaan</h2>

        @forelse ($pesanan->trackingPesanan as $track)
            <div class="border-l-2 border-indigo-200 pl-3 pb-3 text-sm">
                <p class="text-gray-700">{{ $track->status_pengerjaan }}</p>
                <p class="text-gray-400 text-xs">{{ $track->created_at->format('d M Y H:i') }}</p>
                @if ($track->file_progress)
                    <a href="{{ Storage::url($track->file_progress) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Lihat File</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada update progres dari provider.</p>
        @endforelse
    </div>

    {{-- ================= PEMBAYARAN (PB-09) — mahasiswa bayar, konfirmasi oleh provider ================= --}}
    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-600 mb-3">Pembayaran</h2>

        @if ($pesanan->pembayaran)
            <div class="text-sm space-y-1">
                <p class="text-gray-700">Metode: {{ $pesanan->pembayaran->metode_pembayaran }}</p>
                <p class="text-gray-700">Total: Rp {{ number_format($pesanan->pembayaran->total_bayar, 0, ',', '.') }}</p>
                <p @class([
                    'font-medium',
                    'text-yellow-600' => $pesanan->pembayaran->status_bayar === 'menunggu_konfirmasi',
                    'text-green-600' => $pesanan->pembayaran->status_bayar === 'dikonfirmasi',
                    'text-red-600' => $pesanan->pembayaran->status_bayar === 'ditolak',
                ])>
                    Status: {{ ucfirst(str_replace('_',' ', $pesanan->pembayaran->status_bayar)) }}
                </p>
                @if ($pesanan->pembayaran->bukti_pembayaran)
                    <a href="{{ Storage::url($pesanan->pembayaran->bukti_pembayaran) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Lihat Bukti Bayar</a>
                @endif
            </div>
        @elseif ($pesanan->status_pesanan === 'selesai')
            <form method="POST" action="{{ route('pembayaran.store', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-3 max-w-md">
                @csrf
                <input type="text" name="metode_pembayaran" placeholder="Metode pembayaran (mis. Transfer Bank)" required class="w-full border rounded-md px-3 py-2 text-sm">
                <input type="file" name="bukti_pembayaran" required class="w-full text-sm">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Kirim Bukti Pembayaran</button>
            </form>
        @else
            <p class="text-sm text-gray-400">Pembayaran bisa dilakukan setelah pesanan berstatus "selesai".</p>
        @endif
    </div>

    {{-- ================= RATING & REVIEW (PB-09) — mahasiswa ================= --}}
    <div class="bg-white border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-gray-600 mb-3">Rating & Review</h2>

        @if ($pesanan->ratingReview)
            <p class="text-sm text-yellow-600">{{ str_repeat('★', $pesanan->ratingReview->rate) }}{{ str_repeat('☆', 5 - $pesanan->ratingReview->rate) }}</p>
            <p class="text-sm text-gray-700 mt-1">{{ $pesanan->ratingReview->review }}</p>
        @elseif ($pesanan->bolehDireview())
            <form method="POST" action="{{ route('review.store', $pesanan->id_pesanan) }}" class="space-y-3 max-w-md">
                @csrf
                <select name="rate" required class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="">Pilih rating</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} bintang</option>
                    @endfor
                </select>
                <textarea name="review" rows="2" placeholder="Ceritakan pengalaman kamu (opsional)" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Kirim Review</button>
            </form>
        @else
            <p class="text-sm text-gray-400">Review bisa diberikan setelah pesanan selesai & pembayaran dikonfirmasi.</p>
        @endif
    </div>
@endsection
