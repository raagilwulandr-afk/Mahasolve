<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestLayananRequest;
use App\Models\Negosiasi;
use App\Models\Provider;
use App\Models\RequestLayanan;
use Illuminate\Http\Request;

class RequestLayananController extends Controller
{
    public function index()
    {
        return redirect()->route('pesanan.index');
    }

    public function create(Request $request)
    {
        $selectedProvider = null;
        if ($request->filled('provider')) {
            $selectedProvider = Provider::with('user')->find($request->query('provider'));
        }
        $selectedKategori = $request->query('kategori', 'Antar Jemput');

        return view('mahasiswa.request.create', compact('selectedProvider', 'selectedKategori'));
    }

    public function store(StoreRequestLayananRequest $request)
    {
        $validated = $request->validated();

        if ($request->filled('lokasi_jemput') || $request->filled('lokasi_tujuan')) {
            $lokasiText = "\n[Lokasi Jemput: " . ($request->lokasi_jemput ?: '-') . " | Tujuan: " . ($request->lokasi_tujuan ?: '-') . "]";
            $validated['detail_kebutuhan'] .= $lokasiText;
        }

        $validated['id_user'] = auth()->user()->id_user;
        $validated['tanggal_request'] = now();
        $validated['status_request'] = 'open';

        $requestLayanan = RequestLayanan::create($validated);

        if (!empty($validated['id_provider'])) {
            Negosiasi::create([
                'id_request' => $requestLayanan->id_request,
                'id_provider' => $validated['id_provider'],
                'harga_tawaran' => $validated['harga_awal'] ?? 10000,
                'detail_negosiasi' => 'Penawaran awal diajukan otomatis dari Katalog Layanan.',
                'dibuat_oleh' => 'mahasiswa',
                'status_negosiasi' => 'pending',
            ]);
        }

        return redirect()
            ->route('mahasiswa.request.show', $requestLayanan->id_request)
            ->with('success', 'Permintaan layanan berhasil dibuat.');
    }

    public function show($id)
    {
        $requestLayanan = RequestLayanan::findOrFail($id);
        $this->authorizeOwner($requestLayanan);

        $requestLayanan->load('negosiasi.provider.user', 'negosiasi.pesanan');

        return view('mahasiswa.request.show', compact('requestLayanan'));
    }

    public function edit($id)
    {
        $requestLayanan = RequestLayanan::findOrFail($id);
        $this->authorizeOwner($requestLayanan);

        if ($requestLayanan->status_request !== 'open') {
            return back()->with('error', 'Request yang sudah diproses tidak bisa diubah.');
        }

        return view('mahasiswa.request.edit', compact('requestLayanan'));
    }

    public function update(StoreRequestLayananRequest $request, $id)
    {
        $requestLayanan = RequestLayanan::findOrFail($id);
        $this->authorizeOwner($requestLayanan);

        $requestLayanan->update($request->validated());

        return redirect()
            ->route('mahasiswa.request.show', $requestLayanan->id_request)
            ->with('success', 'Permintaan layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $requestLayanan = RequestLayanan::findOrFail($id);
        $this->authorizeOwner($requestLayanan);

        if ($requestLayanan->status_request !== 'open') {
            return back()->with('error', 'Request yang sudah diproses tidak bisa dibatalkan.');
        }

        $requestLayanan->update(['status_request' => 'dibatalkan']);

        return redirect()->route('mahasiswa.request.index')->with('success', 'Permintaan dibatalkan.');
    }

    private function authorizeOwner(RequestLayanan $requestLayanan): void
    {
        $userId = auth()->user()?->id_user ?? auth()->id();
        abort_unless((string) $requestLayanan->id_user === (string) $userId, 403);
    }
}
