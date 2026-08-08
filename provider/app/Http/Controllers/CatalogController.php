<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Provider;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // PB-02: Pencarian Informasi Penyedia Jasa -> jadi fitur browse katalog di dalam sistem
    public function index(Request $request)
    {
        $query = Layanan::with('provider.user')
            ->when($request->kategori, fn ($q) => $q->where('kategori', $request->kategori))
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama_layanan', 'like', '%' . $request->search . '%');
            })
            ->when($request->harga_max, fn ($q) => $q->where('harga', '<=', $request->harga_max));

        // Urutkan berdasarkan rating provider tertinggi (rekomendasi)
        $layanan = $query->join('provider', 'layanan.id_provider', '=', 'provider.id_provider')
            ->orderByDesc('provider.rating')
            ->select('layanan.*')
            ->paginate(12);

        $kategoriList = Layanan::select('kategori')->distinct()->pluck('kategori');

        return view('mahasiswa.catalog.index', compact('layanan', 'kategoriList'));
    }

    // PB-03: Penyaringan & Pemilihan Calon Penyedia Jasa -> lihat detail profil provider
    public function showProvider(Provider $provider)
    {
        $provider->load('layanan', 'user');

        return view('mahasiswa.catalog.provider', compact('provider'));
    }
}
