@extends('layouts.app')

@section('title', 'Detail Request Layanan')

@section('content')
    <div class="bg-white border rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-800">{{ $requestLayanan->kategori }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $requestLayanan->detail_kebutuhan }}</p>
            </div>
            @php
                $statusVal = is_object($requestLayanan->status_request) ? $requestLayanan->status_request->value : (string) $requestLayanan->status_request;
            @endphp
            <span @class([
                'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                'bg-blue-100 text-blue-700' => $statusVal === 'open',
                'bg-yellow-100 text-yellow-700' => $statusVal === 'diproses',
                'bg-green-100 text-green-700' => $statusVal === 'selesai',
                'bg-gray-100 text-gray-500' => $statusVal === 'dibatalkan',
            ])>
                {{ is_object($requestLayanan->status_request) && method_exists($requestLayanan->status_request, 'label') ? $requestLayanan->status_request->label() : ucfirst($statusVal) }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div>
                <dt class="text-gray-400">Perkiraan Budget</dt>
                <dd class="text-gray-700">Rp {{ number_format($requestLayanan->harga_awal ?? 0, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Deadline</dt>
                <dd class="text-gray-700">{{ $requestLayanan->deadline?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div class="col-span-2">
                <dt class="text-gray-400">Kriteria Output</dt>
                <dd class="text-gray-700">{{ $requestLayanan->kriteria_output ?? '-' }}</dd>
            </div>
        </dl>

        @if ($statusVal === 'open')
            <div class="mt-4 flex gap-3">
                <a href="{{ route('catalog.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                    Cari Provider untuk Request Ini
                </a>
                <a href="{{ route('mahasiswa.request.edit', $requestLayanan->id_request) }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">
                    Edit
                </a>
                <form method="POST" action="{{ route('mahasiswa.request.destroy', $requestLayanan->id_request) }}"
                      onsubmit="return confirm('Batalkan request ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-500 hover:text-red-700">Batalkan</button>
                </form>
            </div>
        @endif
    </div>

    <h2 class="text-sm font-semibold text-gray-600 mb-3">Riwayat Negosiasi</h2>

    @if ($requestLayanan->negosiasi->isEmpty())
        <p class="text-sm text-gray-400">Belum ada negosiasi dengan provider manapun.</p>
    @endif

    <div class="space-y-3">
        @foreach ($requestLayanan->negosiasi as $neg)
            <a href="{{ route('negosiasi.show', $neg->id_negosiasi) }}"
               class="block bg-white border rounded-lg p-4 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $neg->provider->user->username }}</p>
                        <p class="text-sm text-gray-500">Tawaran: Rp {{ number_format($neg->harga_tawaran, 0, ',', '.') }}</p>
                    </div>
                    @php
                        $negStatusVal = is_object($neg->status_negosiasi) ? $neg->status_negosiasi->value : (string)$neg->status_negosiasi;
                    @endphp
                    <span @class([
                        'text-xs px-2 py-1 rounded-full font-medium',
                        'bg-yellow-100 text-yellow-700' => $negStatusVal === 'pending',
                        'bg-orange-100 text-orange-700' => $negStatusVal === 'ditawar_ulang',
                        'bg-green-100 text-green-700' => $negStatusVal === 'disepakati',
                        'bg-red-100 text-red-700' => $negStatusVal === 'ditolak',
                    ])>
                        {{ ucfirst(str_replace('_', ' ', $negStatusVal)) }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
@endsection
