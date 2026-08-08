<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\RatingReview;
use Illuminate\Http\Request;

class RatingReviewController extends Controller
{
    // Halaman Riwayat Transaksi: semua pesanan selesai/dibatalkan + ulasan yang sudah/bisa diberikan
    public function index(Request $httpRequest)
    {
        $status = $httpRequest->query('status', 'selesai');
        return redirect()->route('pesanan.index', ['status' => $status]);
    }

    public function store(Request $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless($user->isMahasiswa() && $pesanan->negosiasi->request->id_user === $user->id_user, 403);
        abort_unless($pesanan->bolehDireview(), 400, 'Pesanan ini belum bisa direview.');

        $validated = $request->validate([
            'review' => 'nullable|string|max:1000',
            'rate' => 'required|integer|min:1|max:5',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'review' => $validated['review'] ?? null,
            'rate' => $validated['rate'],
        ]);

        $pesanan->negosiasi->provider->refreshRating();

        return back()->with('success', 'Terima kasih atas ulasannya!');
    }

    // Ubah ulasan yang sudah pernah diberikan
    public function update(Request $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless($user->isMahasiswa() && $pesanan->negosiasi->request->id_user === $user->id_user, 403);
        abort_unless($pesanan->ratingReview, 404);

        $validated = $request->validate([
            'review' => 'nullable|string|max:1000',
            'rate' => 'required|integer|min:1|max:5',
        ]);

        $pesanan->ratingReview->update($validated);
        $pesanan->negosiasi->provider->refreshRating();

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }
}
