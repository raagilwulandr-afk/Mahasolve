<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;

class ReviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $provider = $user->getOrCreateProvider();

        $dataPesanan = Pesanan::with(['negosiasi.request.mahasiswa', 'negosiasi.request', 'ratingReview'])
            ->whereHas('negosiasi', function ($q) use ($provider) {
                $q->where('id_provider', $provider->id_provider);
            })
            ->orderBy('tanggal_pesanan', 'desc')
            ->get();

        $histories = $dataPesanan->map(function ($pesanan) {
            $req = $pesanan->negosiasi?->request;
            $mhs = $req?->mahasiswa;
            $review = $pesanan->ratingReview;
            $statusVal = is_object($pesanan->status_pesanan) ? $pesanan->status_pesanan->value : (string) $pesanan->status_pesanan;

            return (object) [
                'id' => $pesanan->id_pesanan,
                'title' => $req?->detail_kebutuhan ?? 'Pesanan Jasa Mahasolve',
                'status' => match ($statusVal) {
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                    default => 'Diproses',
                },
                'raw_status' => $statusVal,
                'date' => $pesanan->tanggal_pesanan ? $pesanan->tanggal_pesanan->format('d M Y') : now()->format('d M Y'),
                'category' => $req?->kategori ?? 'Umum',
                'customer_name' => $mhs?->username ?? $mhs?->nama ?? 'Mahasiswa',
                'income' => $statusVal === 'dibatalkan' ? 0 : ($pesanan->harga_final ?? 0),
                'has_review' => (bool) $review,
                'rating' => $review?->rate ?? null,
                'review_text' => $review?->review ?? null,
            ];
        });

        $pesananSelesai = $dataPesanan->filter(function ($p) {
            $val = is_object($p->status_pesanan) ? $p->status_pesanan->value : (string) $p->status_pesanan;
            return $val === 'selesai';
        });

        $stats = (object) [
            'total_pesanan' => $pesananSelesai->count(),
            'total_pendapatan' => $pesananSelesai->sum('harga_final'),
            'rating' => number_format($provider->rating ?? 0.0, 1),
        ];

        return view('provider.review', compact('histories', 'stats'));
    }
}