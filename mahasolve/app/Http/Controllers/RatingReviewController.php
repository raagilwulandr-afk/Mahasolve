<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Pesanan;
use App\Services\RatingService;
use Illuminate\Http\Request;

class RatingReviewController extends Controller
{
    public function __construct(
        protected RatingService $ratingService
    ) {}

    public function index(Request $httpRequest)
    {
        $status = $httpRequest->query('status', 'selesai');
        return redirect()->route('pesanan.index', ['status' => $status]);
    }

    public function store(StoreReviewRequest $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless($user->isMahasiswa() && (int) $pesanan->negosiasi?->request?->id_user === (int) $user->id_user, 403);
        abort_unless($pesanan->bolehDireview(), 400, 'Pesanan ini belum bisa direview.');

        $this->ratingService->submitReview(
            pesanan: $pesanan,
            rate: (int) $request->validated('rate'),
            reviewText: $request->validated('review')
        );

        return back()->with('success', 'Terima kasih atas ulasannya!');
    }

    public function update(StoreReviewRequest $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless($user->isMahasiswa() && (int) $pesanan->negosiasi?->request?->id_user === (int) $user->id_user, 403);
        abort_unless($pesanan->ratingReview, 404);

        $this->ratingService->updateReview(
            ratingReview: $pesanan->ratingReview,
            rate: (int) $request->validated('rate'),
            reviewText: $request->validated('review')
        );

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }
}
