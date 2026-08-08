<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RatingReview;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public const KATEGORI = [
        ['nama' => 'Antar Jemput', 'warna' => '#4F46E5', 'icon' => '<path d="M4 16l4-5 3 3 5-6 4 5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
        ['nama' => 'Print & Fotokopi', 'warna' => '#14B8A6', 'icon' => '<rect x="5" y="9" width="14" height="8" rx="1.5" stroke="#fff" stroke-width="2"/><path d="M7 9V4h10v5M7 17v3h10v-3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
        ['nama' => 'Bimbingan', 'warna' => '#F59E0B', 'icon' => '<path d="M4 6l8-3 8 3-8 3-8-3z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M6 10v6c0 1 2.7 2 6 2s6-1 6-2v-6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'],
        ['nama' => 'Desain & Editing', 'warna' => '#EC4899', 'icon' => '<path d="M14 4l4 4-9 9-4.5.5.5-4.5L14 4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>'],
        ['nama' => 'Titip Makan', 'warna' => '#EF4444', 'icon' => '<path d="M6 3v7a2 2 0 002 2v9M6 3v5M8 3v5M10 3v5M10 3v18M18 3c-2 1-3 3-3 6s1 4 3 5v9" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>'],
        ['nama' => 'Titip Beli', 'warna' => '#8B5CF6', 'icon' => '<path d="M6 8h12l-1 12H7L6 8z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M9 8V6a3 3 0 016 0v2" stroke="#fff" stroke-width="2"/>'],
    ];

    public function index(Request $request)
    {
        $kategoriList = self::KATEGORI;
        $kategoriAktif = $request->query('kategori', $kategoriList[0]['nama']);

        $providers = Provider::whereHas('layanan', fn ($q) => $q->where('kategori', $kategoriAktif))
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($qq) use ($search) {
                    $qq->whereHas('user', fn ($u) => $u->where('username', 'like', "%{$search}%"))
                       ->orWhereHas('layanan', fn ($l) => $l->where('nama_layanan', 'like', "%{$search}%"));
                });
            })
            ->with(['user', 'layanan' => fn ($q) => $q->where('kategori', $kategoriAktif)->orderBy('harga')])
            ->withCount([
                'negosiasi as order_count' => fn ($q) => $q->whereHas('pesanan', fn ($p) => $p->where('status_pesanan', 'selesai')),
                'negosiasi as review_count' => fn ($q) => $q->whereHas('pesanan.ratingReview'),
            ])
            ->orderByDesc('rating')
            ->get()
            ->map(function ($provider) {
                $provider->harga_mulai = $provider->layanan->min('harga');
                return $provider;
            });

        return view('mahasiswa.catalog.index', compact('providers', 'kategoriList', 'kategoriAktif'));
    }

    public function showProvider(Provider $provider)
    {
        $provider->load('layanan', 'user');

        return view('mahasiswa.catalog.provider', compact('provider'));
    }

    // FASE 2: Instant Checkout / Direct Order dari Katalog
    public function storeDirectOrder(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $layanan = Layanan::with('provider')->findOrFail($request->id_layanan);

        // 1. Buat RequestLayanan otomatis
        $reqLayanan = RequestLayanan::create([
            'id_user' => $user->id_user,
            'kategori' => $layanan->kategori,
            'detail_kebutuhan' => "Pembelian Layanan: {$layanan->nama_layanan}" . ($request->catatan ? " ({$request->catatan})" : ""),
            'harga_awal' => $layanan->harga,
            'status_request' => 'diproses',
        ]);

        // 2. Buat Negosiasi disepakati secara instan
        $nego = Negosiasi::create([
            'id_request' => $reqLayanan->id_request,
            'id_provider' => $layanan->id_provider,
            'harga_tawaran' => $layanan->harga,
            'detail_negosiasi' => 'Pembelian langsung dari Katalog Layanan (Instant Checkout)',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        // 3. Buat Pesanan
        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => $layanan->harga,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        // 4. Buat TrackingPesanan awal
        TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => 'Pesanan berhasil dibuat dari Katalog. Menunggu pengerjaan provider.',
        ]);

        return redirect()->route('pesanan.show', $pesanan->id_pesanan)
            ->with('success', 'Pesanan langsung berhasil dibuat! Silakan lanjutkan pembayaran.');
    }
}
