<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;
use App\Models\Provider;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index()
    {
        // 1. Dapatkan id_user yang sedang login
        $userId = Auth::id(); 

        // 2. Cari id_provider dari user tersebut
        $provider = Provider::where('id_user', $userId)->first();

        // Jika user yang login ternyata bukan provider, kembalikan error/redirect
        if (!$provider) {
            abort(403, 'Akses ditolak. Anda bukan penyedia jasa.');
        }

        // 3. Ambil Data Riwayat dari Database menggunakan nama relasi dari Model Pesanan
        // Memanggil 'mahasiswa' dan 'review' sesuai yang ada di model
        $dataPesanan = Pesanan::with(['layanan', 'mahasiswa', 'review'])
            ->where('id_provider', $provider->id_provider)
            ->whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderBy('tanggal_pesanan', 'desc')
            ->get();

        // 4. Format Data (Mapping) untuk dilempar ke View
        $histories = $dataPesanan->map(function ($pesanan) {
            return (object) [
                'id' => $pesanan->id_pesanan,
                'title' => $pesanan->layanan->nama_layanan ?? 'Layanan Mahasolve',
                'status' => ucfirst($pesanan->status_pesanan),
                'date' => $pesanan->tanggal_pesanan->format('d M Y'), // Karena di model sudah dicast ke datetime
                'category' => $pesanan->layanan->kategori ?? 'Umum',
                'customer_name' => $pesanan->mahasiswa->nama ?? 'Mahasiswa', // Menggunakan relasi 'mahasiswa'
                'income' => $pesanan->harga_final ?? 0, 
                'has_review' => $pesanan->review ? true : false, // Menggunakan relasi 'review'
                'rating' => $pesanan->review->rating ?? 0,
                'review_text' => $pesanan->review->review ?? null,
            ];
        });

        // 5. Hitung Data Statistik (Hanya dihitung dari pesanan 'Selesai')
        $pesananSelesai = $histories->where('status', 'Selesai');
        
        $stats = (object) [
            'total_pesanan' => $pesananSelesai->count(),
            'total_pendapatan' => $pesananSelesai->sum('income'),
            // Hitung rata-rata rating, format ke 1 angka desimal
            'rating' => $pesananSelesai->where('has_review', true)->avg('rating') 
                        ? number_format($pesananSelesai->where('has_review', true)->avg('rating'), 1) 
                        : 0
        ];

        // 6. Kirim ke View review.blade.php
        return view('provider.review', compact('histories', 'stats'));
    }
}