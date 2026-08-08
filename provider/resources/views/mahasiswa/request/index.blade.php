@extends('layouts.app')

@section('title', 'Request Layanan Saya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800">Request Layanan Saya</h1>
        <a href="{{ route('mahasiswa.request.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
            + Buat Request Baru
        </a>
    </div>

    @if ($requests->isEmpty())
        <p class="text-gray-500 text-sm">Belum ada request layanan. Yuk buat yang pertama.</p>
    @endif

    <div class="space-y-3">
        @foreach ($requests as $req)
            <a href="{{ route('mahasiswa.request.show', $req->id_request) }}"
               class="block bg-white border rounded-lg p-4 hover:shadow-sm transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $req->kategori }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($req->detail_kebutuhan, 100) }}</p>
                        <p class="text-xs text-gray-400 mt-2">
                            Deadline: {{ $req->deadline?->format('d M Y') ?? '-' }} ·
                            Diajukan: {{ $req->tanggal_request->format('d M Y') }}
                        </p>
                    </div>
                    <span @class([
                        'text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap',
                        'bg-blue-100 text-blue-700' => $req->status_request === 'open',
                        'bg-yellow-100 text-yellow-700' => $req->status_request === 'diproses',
                        'bg-green-100 text-green-700' => $req->status_request === 'selesai',
                        'bg-gray-100 text-gray-500' => $req->status_request === 'dibatalkan',
                    ])>
                        {{ ucfirst($req->status_request) }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $requests->links() }}
    </div>
@endsection
