<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\CatalogService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CatalogService $catalogService
    ) {}

    public function index(Request $request)
    {
        $kategoriList = $this->catalogService->getCategories();
        $kategoriAktif = $request->query('kategori', $kategoriList[0]['nama']);

        $providers = $this->catalogService->searchProviders(
            kategoriAktif: $kategoriAktif,
            searchTerm: $request->search
        );

        return view('mahasiswa.catalog.index', compact('providers', 'kategoriList', 'kategoriAktif'));
    }

    public function showProvider(Provider $provider)
    {
        $provider->load('layanan', 'user');

        return view('mahasiswa.catalog.provider', compact('provider'));
    }

    public function storeDirectOrder(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pesanan = $this->orderService->createDirectOrder(
            user: Auth::user(),
            layananId: (int) $request->id_layanan,
            catatan: $request->catatan
        );

        return redirect()->route('pesanan.show', $pesanan->id_pesanan)
            ->with('success', 'Pesanan langsung berhasil dibuat! Silakan lanjutkan pembayaran.');
    }
}
