<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Layanan;

class ServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $provider = $user->getOrCreateProvider();

        $services = Layanan::where('id_provider', $provider->id_provider)->latest()->get();
        $totalLayanan = $services->count();
        $rataRataRating = $provider->rating ?? 0.0;
        $totalOrder = \App\Models\Negosiasi::where('id_provider', $provider->id_provider)->count();
        $notifications = collect([]);

        return view('provider.my-service', compact(
            'services',
            'totalLayanan',
            'totalOrder',
            'rataRataRating',
            'notifications',
            'provider'
        ));
    }

    public function store(StoreServiceRequest $request)
    {
        $user = auth()->user();
        $provider = $user->getOrCreateProvider();
        $validated = $request->validated();

        Layanan::create([
            'id_provider' => $provider->id_provider,
            'nama_layanan' => $validated['nama_layanan'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'estimasi_pengerjaan' => $validated['estimasi_pengerjaan'] ?? '1 hari',
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $user = auth()->user();
        $provider = $user->getOrCreateProvider();
        $validated = $request->validated();

        $layanan = Layanan::where('id_provider', $provider->id_provider)
            ->where('id_layanan', $id)
            ->firstOrFail();

        $layanan->update([
            'nama_layanan' => $validated['nama_layanan'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $provider = $user->getOrCreateProvider();

        Layanan::where('id_provider', $provider->id_provider)
            ->where('id_layanan', $id)
            ->firstOrFail()
            ->delete();

        return back()->with('success', 'Layanan berhasil dihapus.');
    }
}