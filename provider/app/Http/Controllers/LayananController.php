<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $provider = $request->user()->provider;

        abort_if(!$provider, 403, 'Data provider tidak ditemukan.');

        $layanan = Layanan::query()
            ->where('id_provider', $provider->id_provider)
            ->latest('tanggal_dibuat')
            ->paginate(10);

        return view('my-service', compact(
            'provider',
            'layanan'
        ));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $provider = $request->user()->provider;

        abort_if(!$provider, 403, 'Data provider tidak ditemukan.');

        $validated = $request->validate([
            'nama_layanan' => [
                'required',
                'string',
                'max:100',
            ],
            'deskripsi' => [
                'required',
                'string',
            ],
            'kategori' => [
                'required',
                'string',
                'max:50',
            ],
            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],
            'estimasi_pengerjaan' => [
                'required',
                'string',
                'max:50',
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ]);

        Layanan::create([
            'id_provider' => $provider->id_provider,
            'id_user' => $request->user()->id_user,
            'nama_layanan' => $validated['nama_layanan'],
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'estimasi_pengerjaan' => $validated['estimasi_pengerjaan'],
            'status' => $validated['status'],
            'tanggal_dibuat' => now(),
        ]);

        return redirect()
            ->route('my-service')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Request $request, Layanan $layanan)
    {
        $this->authorizeProvider($request, $layanan);

        return view('services.edit', compact('layanan'));
    }

    public function update(
        Request $request,
        Layanan $layanan
    ) {
        $this->authorizeProvider($request, $layanan);

        $validated = $request->validate([
            'nama_layanan' => [
                'required',
                'string',
                'max:100',
            ],
            'deskripsi' => [
                'required',
                'string',
            ],
            'kategori' => [
                'required',
                'string',
                'max:50',
            ],
            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],
            'estimasi_pengerjaan' => [
                'required',
                'string',
                'max:50',
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ]);

        $layanan->update($validated);

        return redirect()
            ->route('my-service')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        Layanan $layanan
    ) {
        $this->authorizeProvider($request, $layanan);

        if ($layanan->pesanan()->exists()) {
            return back()->with(
                'error',
                'Layanan tidak dapat dihapus karena sudah memiliki pesanan.'
            );
        }

        $layanan->delete();

        return redirect()
            ->route('my-service')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    public function toggleStatus(
        Request $request,
        Layanan $layanan
    ) {
        $this->authorizeProvider($request, $layanan);

        $layanan->update([
            'status' => $layanan->status === 'aktif'
                ? 'nonaktif'
                : 'aktif',
        ]);

        return back()->with(
            'success',
            'Status layanan berhasil diubah.'
        );
    }

    private function authorizeProvider(
        Request $request,
        Layanan $layanan
    ): void {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $layanan->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke layanan ini.'
        );
    }
}
