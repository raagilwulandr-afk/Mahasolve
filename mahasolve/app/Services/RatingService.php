<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\RatingReview;

class RatingService
{
    public function submitReview(Pesanan $pesanan, int $rate, ?string $reviewText = null): RatingReview
    {
        $ratingReview = RatingReview::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'review' => $reviewText,
            'rate' => $rate,
        ]);

        $pesanan->negosiasi?->provider?->refreshRating();

        return $ratingReview;
    }

    public function updateReview(RatingReview $ratingReview, int $rate, ?string $reviewText = null): RatingReview
    {
        $ratingReview->update([
            'rate' => $rate,
            'review' => $reviewText,
        ]);

        $ratingReview->pesanan?->negosiasi?->provider?->refreshRating();

        return $ratingReview;
    }
}
