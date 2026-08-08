<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\RatingReview;
use App\Models\RequestLayanan;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProviderDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $provider = $user->provider;

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

        // Variabel Statistik Murni
        $totalPendapatan = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'selesai')
        ->sum('harga_final') ?? 0;

        $pesananAktif = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->whereIn('status_pesanan', ['menunggu_pengerjaan', 'dikerjakan', 'revisi'])
        ->count();

        $pesananBaru = Negosiasi::where('id_provider', $provider->id_provider)
            ->where('status_negosiasi', 'pending')
            ->count();

        $pesananSelesai = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'selesai')
        ->count();

        $pesananBatal = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'dibatalkan')
        ->count();

        $rating = $provider->rating ?? 0.0;
        $rataRataRating = $rating;

        $jumlahReview = RatingReview::whereHas('pesanan.negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })->count();

        $jumlahLayanan = $provider->layanan()->count();
        $totalLayanan = $jumlahLayanan;
        $saldo = $totalPendapatan;

        $layananAktif = Pesanan::with(['negosiasi.request.mahasiswa', 'trackingPesanan', 'detailPekerjaan'])
            ->whereHas('negosiasi', function ($q) use ($provider) {
                $q->where('id_provider', $provider->id_provider);
            })
            ->whereIn('status_pesanan', ['dikerjakan', 'revisi', 'menunggu_pengerjaan'])
            ->latest('tanggal_pesanan')
            ->first();

        // Permintaan Baru (Matching / Requests)
        $requestsList = RequestLayanan::with('mahasiswa')
            ->where('status_request', 'diproses')
            ->latest('tanggal_request')
            ->take(5)
            ->get();

        $permintaanBaru = $requestsList->map(function ($req) {
            return (object) [
                'id' => $req->id_request,
                'requestLayanan' => $req,
                'mahasiswa' => $req->mahasiswa,
                'tanggal_matching' => $req->tanggal_request ? Carbon::parse($req->tanggal_request) : now(),
            ];
        });

        $listPermintaanBaru = $permintaanBaru;
        $requests = $permintaanBaru;
        $orders = collect([]);
        $pesananTerbaru = collect([]);
        $transaksiTerbaru = collect([]);
        $layananPopuler = collect([]);
        $notifikasi = collect([]);
        $mahasiswa = $user;

        return view('provider.dashboard', compact(
            'provider',
            'user',
            'mahasiswa',
            'totalPendapatan',
            'pesananAktif',
            'pesananBaru',
            'pesananSelesai',
            'pesananBatal',
            'rating',
            'rataRataRating',
            'jumlahReview',
            'jumlahLayanan',
            'totalLayanan',
            'saldo',
            'layananAktif',
            'permintaanBaru',
            'listPermintaanBaru',
            'requests',
            'orders',
            'pesananTerbaru',
            'transaksiTerbaru',
            'layananPopuler',
            'notifikasi'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'metode' => 'required|string',
            'no_rekening' => 'required|string|max:50',
            'jumlah' => 'required|numeric|min:10000',
        ]);

        return redirect()->back()->with('success', 'Penarikan saldo sebesar Rp' . number_format($request->jumlah, 0, ',', '.') . ' ke ' . $request->metode . ' (' . $request->no_rekening . ') berhasil diproses!');
    }
}
