<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

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

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'estimasi_pengerjaan' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

        Layanan::create([
            'id_provider' => $provider->id_provider,
            'nama_layanan' => $request->nama_layanan,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'estimasi_pengerjaan' => $request->estimasi_pengerjaan ?? '1 hari',
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $user = Auth::user();
        $provider = $user->provider;

        if ($provider) {
            $layanan = Layanan::where('id_provider', $provider->id_provider)
                ->where('id_layanan', $id)
                ->firstOrFail();

            $layanan->update([
                'nama_layanan' => $request->nama_layanan,
                'kategori' => $request->kategori,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
            ]);
        }

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $provider = $user->provider;

        if ($provider) {
            Layanan::where('id_provider', $provider->id_provider)
                ->where('id_layanan', $id)
                ->delete();
        }

        return back()->with('success', 'Layanan berhasil dihapus.');
    }
}