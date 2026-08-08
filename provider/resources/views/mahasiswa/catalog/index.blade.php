@extends('layouts.app')

@section('title', 'Katalog Layanan')

@section('content')
    <h1 class="text-xl font-bold text-gray-800 mb-6">Katalog Layanan</h1>

    <form method="GET" class="bg-white border rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama layanan..."
                   class="border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select name="kategori" class="border rounded-md px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($kategoriList as $kat)
                    <option value="{{ $kat }}" @selected(request('kategori') === $kat)>{{ $kat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Harga Maks (Rp)</label>
            <input type="number" name="harga_max" value="{{ request('harga_max') }}"
                   class="border rounded-md px-3 py-2 text-sm w-32">
        </div>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Filter</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($layanan as $item)
            <div class="bg-white border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full">{{ $item->kategori }}</span>
                    <span class="text-xs text-yellow-600">★ {{ number_format($item->provider->rating, 1) }}</span>
                </div>
                <h3 class="font-semibold text-gray-800">{{ $item->nama_layanan }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($item->deskripsi, 90) }}</p>
                <div class="flex items-center justify-between mt-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">Estimasi: {{ $item->estimasi_pengerjaan ?? '-' }}</p>
                    </div>
                    <a href="{{ route('catalog.provider', $item->id_provider) }}" class="text-sm text-indigo-600 hover:underline">
                        {{ $item->provider->user->username }} &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $layanan->links() }}
    </div>
@endsection
