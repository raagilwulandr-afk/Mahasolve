@extends('layouts.app')

@section('title', 'Edit Request Layanan')

@section('content')
    <h1 class="text-xl font-bold text-gray-800 mb-6">Edit Request Layanan</h1>

    <form method="POST" action="{{ route('mahasiswa.request.update', $requestLayanan->id_request) }}" class="bg-white border rounded-lg p-6 space-y-4 max-w-xl">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <input type="text" name="kategori" value="{{ old('kategori', $requestLayanan->kategori) }}"
                   class="w-full border rounded-md px-3 py-2 text-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kebutuhan</label>
            <textarea name="detail_kebutuhan" rows="4" class="w-full border rounded-md px-3 py-2 text-sm" required>{{ old('detail_kebutuhan', $requestLayanan->detail_kebutuhan) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kriteria Output</label>
            <textarea name="kriteria_output" rows="2" class="w-full border rounded-md px-3 py-2 text-sm">{{ old('kriteria_output', $requestLayanan->kriteria_output) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Budget (Rp)</label>
                <input type="number" name="harga_awal" value="{{ old('harga_awal', $requestLayanan->harga_awal) }}" min="0"
                       class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $requestLayanan->deadline?->format('Y-m-d')) }}"
                       class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md text-sm hover:bg-indigo-700">
                Simpan Perubahan
            </button>
            <a href="{{ route('mahasiswa.request.show', $requestLayanan->id_request) }}" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">
                Batal
            </a>
        </div>
    </form>
@endsection
