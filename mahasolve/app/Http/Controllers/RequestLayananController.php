<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\RequestLayanan;
use Illuminate\Http\Request;

class RequestLayananController extends Controller
{
    // Redirect ke tab "Request Saya" di halaman Order (biar list-nya nggak duplikat)
    public function index()
    {
        return redirect()->route('pesanan.index', ['tab' => 'request']);
    }

    public function create()
    {
        return view('mahasiswa.request.create');
    }

    // PB-01: Identifikasi Kebutuhan Layanan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'detail_kebutuhan' => 'required|string',
            'kategori' => 'required|string|max:100',
            'harga_awal' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'kriteria_output' => 'nullable|string',
        ]);

        $validated['id_user'] = auth()->id();
        $validated['tanggal_request'] = now();
        $validated['status_request'] = 'open';

        $requestLayanan = RequestLayanan::create($validated);

        return redirect()
            ->route('mahasiswa.request.show', $requestLayanan->id_request)
            ->with('success', 'Permintaan layanan berhasil dibuat.');
    }

    public function show(RequestLayanan $requestLayanan)
    {
        $this->authorizeOwner($requestLayanan);

        $requestLayanan->load('negosiasi.provider.user', 'negosiasi.pesanan');

        return view('mahasiswa.request.show', compact('requestLayanan'));
    }

    public function edit(RequestLayanan $requestLayanan)
    {
        $this->authorizeOwner($requestLayanan);

        if ($requestLayanan->status_request !== 'open') {
            return back()->with('error', 'Request yang sudah diproses tidak bisa diubah.');
        }

        return view('mahasiswa.request.edit', compact('requestLayanan'));
    }

    public function update(Request $request, RequestLayanan $requestLayanan)
    {
        $this->authorizeOwner($requestLayanan);

        $validated = $request->validate([
            'detail_kebutuhan' => 'required|string',
            'kategori' => 'required|string|max:100',
            'harga_awal' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'kriteria_output' => 'nullable|string',
        ]);

        $requestLayanan->update($validated);

        return redirect()
            ->route('mahasiswa.request.show', $requestLayanan->id_request)
            ->with('success', 'Permintaan layanan berhasil diperbarui.');
    }

    public function destroy(RequestLayanan $requestLayanan)
    {
        $this->authorizeOwner($requestLayanan);

        if ($requestLayanan->status_request !== 'open') {
            return back()->with('error', 'Request yang sudah diproses tidak bisa dibatalkan.');
        }

        $requestLayanan->update(['status_request' => 'dibatalkan']);

        return redirect()->route('mahasiswa.request.index')->with('success', 'Permintaan dibatalkan.');
    }

    private function authorizeOwner(RequestLayanan $requestLayanan): void
    {
        abort_unless($requestLayanan->id_user === auth()->id(), 403);
    }
}
