<?php

namespace App\Http\Controllers;

use App\Models\MatchingProvider;
use App\Models\Pesanan;
use App\Models\RatingReview;
use Illuminate\Http\Request;

class ProviderDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $provider = $user->provider;

        if (!$provider) {
            abort(403, 'Data provider tidak ditemukan.');
        }

        $totalPendapatan = Pesanan::query()
            ->where('id_provider', $provider->id_provider)
            ->where('status_pesanan', 'selesai')
            ->whereHas('pembayaran', function ($query) {
                $query->where('status_bayar', 'berhasil');
            })
            ->sum('total_harga');

        $pesananAktif = Pesanan::query()
            ->where('id_provider', $provider->id_provider)
            ->whereIn('status_pesanan', [
                'menunggu pembayaran',
                'diproses',
                'revisi',
            ])
            ->count();

        $pesananBaru = Pesanan::query()
            ->where('id_provider', $provider->id_provider)
            ->where('status_pesanan', 'menunggu pembayaran')
            ->count();

        $rating = RatingReview::query()
            ->whereHas('pesanan', function ($query) use ($provider) {
                $query->where(
                    'id_provider',
                    $provider->id_provider
                );
            })
            ->avg('rating');

        $jumlahReview = RatingReview::query()
            ->whereHas('pesanan', function ($query) use ($provider) {
                $query->where(
                    'id_provider',
                    $provider->id_provider
                );
            })
            ->count();

        $permintaanBaru = MatchingProvider::query()
            ->with([
                'requestLayanan.mahasiswa',
                'requestLayanan.layanan',
            ])
            ->where('id_provider', $provider->id_provider)
            ->where('status_matching', 'direkomendasikan')
            ->whereHas('requestLayanan', function ($query) {
                $query->whereIn('status_request', [
                    'pending',
                    'dicocokan',
                ]);
            })
            ->latest('tanggal_matching')
            ->take(5)
            ->get();

        $layananAktif = Pesanan::query()
            ->with([
                'mahasiswa',
                'layanan',
                'trackingTerbaru',
                'detailPekerjaan',
            ])
            ->where('id_provider', $provider->id_provider)
            ->whereIn('status_pesanan', [
                'diproses',
                'revisi',
            ])
            ->latest('tanggal_pesanan')
            ->first();

        $jumlahLayanan = $provider
            ->layanan()
            ->where('status', 'aktif')
            ->count();

        return view('dashboard', compact(
            'provider',
            'totalPendapatan',
            'pesananAktif',
            'pesananBaru',
            'rating',
            'jumlahReview',
            'permintaanBaru',
            'layananAktif',
            'jumlahLayanan'
        ));
    }
}
