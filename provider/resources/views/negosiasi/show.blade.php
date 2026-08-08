@extends('layouts.app')

@section('title', 'Detail Negosiasi')

@section('content')
    <div class="bg-white border rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Negosiasi #{{ $negosiasi->id_negosiasi }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $negosiasi->request->mahasiswa->username }} &harr; {{ $negosiasi->provider->user->username }}
                </p>
            </div>
            <span @class([
                'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                'bg-yellow-100 text-yellow-700' => $negosiasi->status_negosiasi === 'pending',
                'bg-orange-100 text-orange-700' => $negosiasi->status_negosiasi === 'ditawar_ulang',
                'bg-green-100 text-green-700' => $negosiasi->status_negosiasi === 'disepakati',
                'bg-red-100 text-red-700' => $negosiasi->status_negosiasi === 'ditolak',
            ])>
                {{ ucfirst(str_replace('_', ' ', $negosiasi->status_negosiasi)) }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div>
                <dt class="text-gray-400">Kebutuhan</dt>
                <dd class="text-gray-700">{{ $negosiasi->request->detail_kebutuhan }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Harga Tawaran Saat Ini</dt>
                <dd class="text-gray-700 font-medium">Rp {{ number_format($negosiasi->harga_tawaran, 0, ',', '.') }}</dd>
            </div>
            <div class="col-span-2">
                <dt class="text-gray-400">Catatan Negosiasi</dt>
                <dd class="text-gray-700">{{ $negosiasi->detail_negosiasi ?? '-' }}</dd>
            </div>
        </dl>

        @if ($negosiasi->pesanan)
            <a href="{{ route('pesanan.show', $negosiasi->pesanan->id_pesanan) }}"
               class="inline-block mt-4 text-sm text-indigo-600 hover:underline">
                Sudah jadi pesanan &rarr; lihat detail pesanan
            </a>
        @elseif (in_array($negosiasi->status_negosiasi, ['pending', 'ditawar_ulang']))
            <div class="mt-6 border-t pt-6 space-y-6">

                <form method="POST" action="{{ route('negosiasi.accept', $negosiasi->id_negosiasi) }}" class="inline">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                        Setuju dengan Harga Ini
                    </button>
                </form>

                <form method="POST" action="{{ route('negosiasi.reject', $negosiasi->id_negosiasi) }}" class="inline"
                      onsubmit="return confirm('Batalkan negosiasi ini?')">
                    @csrf
                    <button class="border border-red-400 text-red-500 px-4 py-2 rounded-md text-sm hover:bg-red-50">
                        Tolak / Batalkan
                    </button>
                </form>

                <form method="POST" action="{{ route('negosiasi.counter', $negosiasi->id_negosiasi) }}" class="mt-4 max-w-md space-y-3">
                    @csrf
                    <p class="text-sm font-medium text-gray-700">Atau ajukan tawaran baru:</p>
                    <input type="number" name="harga_tawaran" min="0" placeholder="Harga baru (Rp)" required
                           class="w-full border rounded-md px-3 py-2 text-sm">
                    <textarea name="detail_negosiasi" rows="2" placeholder="Catatan tambahan (opsional)"
                              class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                        Kirim Tawaran Baru
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
