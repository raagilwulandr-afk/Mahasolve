<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\RatingReview;
use App\Models\RequestLayanan;
use App\Services\ProviderService;
use Illuminate\Http\Request;

class ProviderDashboardController extends Controller
{
    public function __construct(
        protected ProviderService $providerService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $provider = $user->getOrCreateProvider();

        $stats = $this->providerService->getDashboardStats($provider);

        $totalPendapatan = $stats->totalPendapatan;
        $pesananAktif = $stats->pesananAktif;
        $pesananSelesai = $stats->pesananSelesai;
        $saldo = $stats->saldoBisaDitarik;

        $pesananBaru = Negosiasi::where('id_provider', $provider->id_provider)
            ->where('status_negosiasi', 'pending')
            ->count();

        $pesananBatal = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'dibatalkan')
        ->count();

        $rataRataRating = $provider->rating ?? 0.0;

        $jumlahReview = RatingReview::whereHas('pesanan.negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })->count();

        $totalLayanan = $provider->layanan()->count();

        $incomingRequests = RequestLayanan::with('mahasiswa')
            ->where('status_request', 'open')
            ->latest()
            ->take(6)
            ->get();

        $activeOrders = Pesanan::with(['negosiasi.request.mahasiswa', 'negosiasi.provider'])
            ->whereHas('negosiasi', function ($q) use ($provider) {
                $q->where('id_provider', $provider->id_provider);
            })
            ->latest()
            ->take(5)
            ->get();

        $permintaanBaru = $incomingRequests;
        $layananAktif = $activeOrders->first();

        return view('provider.dashboard', compact(
            'totalPendapatan',
            'pesananAktif',
            'pesananBaru',
            'pesananSelesai',
            'pesananBatal',
            'rataRataRating',
            'jumlahReview',
            'totalLayanan',
            'saldo',
            'incomingRequests',
            'permintaanBaru',
            'layananAktif',
            'activeOrders'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:10000',
            'metode' => 'required|string',
            'no_rekening' => 'required|string',
        ]);

        return redirect()->back()->with('success', 'Penarikan saldo sebesar Rp' . number_format($request->jumlah, 0, ',', '.') . ' ke ' . $request->metode . ' (' . $request->no_rekening . ') berhasil diproses!');
    }
}
