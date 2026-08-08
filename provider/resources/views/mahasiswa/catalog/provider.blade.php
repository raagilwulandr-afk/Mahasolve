@extends('layouts.app')

@section('title', $provider->user->username)

@section('content')
    <div class="bg-white border rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-800">{{ $provider->user->username }}</h1>
                <p class="text-sm text-yellow-600 mt-1">★ {{ number_format($provider->rating, 1) }} rating</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mt-3">{{ $provider->detail_provider }}</p>
    </div>

    <h2 class="text-sm font-semibold text-gray-600 mb-3">Layanan yang Ditawarkan</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @foreach ($provider->layanan as $item)
            <div class="bg-white border rounded-lg p-4">
                <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full">{{ $item->kategori }}</span>
                <h3 class="font-semibold text-gray-800 mt-2">{{ $item->nama_layanan }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $item->deskripsi }}</p>
                <p class="text-sm font-medium text-gray-700 mt-2">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    @php $openRequests = auth()->user()->requestLayanan()->where('status_request', 'open')->get(); @endphp

    <h2 class="text-sm font-semibold text-gray-600 mb-3">Ajukan Permintaan ke Provider Ini</h2>

    @if ($openRequests->isEmpty())
        <p class="text-sm text-gray-400">
            Kamu belum punya request layanan yang berstatus "open".
            <a href="{{ route('mahasiswa.request.create') }}" class="text-indigo-600 hover:underline">Buat request baru</a>.
        </p>
    @else
        <form method="POST" action="" id="negosiasi-form" class="bg-white border rounded-lg p-6 space-y-4 max-w-lg">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Request Layanan</label>
                <select name="id_request" required
                        onchange="document.getElementById('negosiasi-form').action = '/request/' + this.value + '/negosiasi/{{ $provider->id_provider }}'"
                        class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="">-- pilih --</option>
                    @foreach ($openRequests as $req)
                        <option value="{{ $req->id_request }}">{{ $req->kategori }} — {{ Str::limit($req->detail_kebutuhan, 40) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga yang Ditawarkan (Rp)</label>
                <input type="number" name="harga_tawaran" min="0" required class="w-full border rounded-md px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <textarea name="detail_negosiasi" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md text-sm hover:bg-indigo-700">
                Ajukan Permintaan
            </button>
        </form>
    @endif
@endsection
