@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <h1 class="text-xl font-bold text-gray-800 mb-6">Pesanan</h1>

    @if ($pesanan->isEmpty())
        <p class="text-gray-500 text-sm">Belum ada pesanan.</p>
    @endif

    <div class="space-y-3">
        @foreach ($pesanan as $p)
            <a href="{{ route('pesanan.show', $p->id_pesanan) }}"
               class="block bg-white border rounded-lg p-4 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $p->negosiasi->provider->user->username }}</p>
                        <p class="text-sm text-gray-500">Rp {{ number_format($p->harga_final, 0, ',', '.') }} · {{ $p->tanggal_pesanan->format('d M Y') }}</p>
                    </div>
                    <span @class([
                        'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                        'bg-blue-100 text-blue-700' => $p->status_pesanan === 'menunggu_pengerjaan',
                        'bg-yellow-100 text-yellow-700' => $p->status_pesanan === 'dikerjakan',
                        'bg-orange-100 text-orange-700' => $p->status_pesanan === 'revisi',
                        'bg-green-100 text-green-700' => $p->status_pesanan === 'selesai',
                        'bg-gray-100 text-gray-500' => $p->status_pesanan === 'dibatalkan',
                    ])>
                        {{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $pesanan->links() }}
    </div>
@endsection
