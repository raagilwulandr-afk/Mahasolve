<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

        $dataPesanan = Pesanan::with(['negosiasi.request.mahasiswa', 'ratingReview'])
            ->whereHas('negosiasi', function ($q) use ($provider) {
                $q->where('id_provider', $provider->id_provider);
            })
            ->whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderBy('tanggal_pesanan', 'desc')
            ->get();

        $histories = $dataPesanan->map(function ($pesanan) {
            $mhs = $pesanan->mahasiswa();
            $review = $pesanan->ratingReview;

            return (object) [
                'id' => $pesanan->id_pesanan,
                'title' => $pesanan->negosiasi->request->detail_kebutuhan ?? 'Pesanan Jasa Mahasolve',
                'status' => ucfirst($pesanan->status_pesanan),
                'date' => $pesanan->tanggal_pesanan ? $pesanan->tanggal_pesanan->format('d M Y') : now()->format('d M Y'),
                'category' => $pesanan->negosiasi->request->kategori ?? 'Umum',
                'customer_name' => $mhs->username ?? 'Mahasiswa',
                'income' => $pesanan->harga_final ?? 0,
                'has_review' => $review ? true : false,
                'rating' => $review->rate ?? 0,
                'review_text' => $review->review ?? null,
            ];
        });

        $pesananSelesai = $histories->where('status', 'Selesai');

        $stats = (object) [
            'total_pesanan' => $pesananSelesai->count(),
            'total_pendapatan' => $pesananSelesai->sum('income'),
            'rating' => $pesananSelesai->where('has_review', true)->avg('rating')
                ? number_format($pesananSelesai->where('has_review', true)->avg('rating'), 1)
                : number_format($provider->rating ?? 0, 1),
        ];

        return view('provider.review', compact('histories', 'stats'));
    }
}