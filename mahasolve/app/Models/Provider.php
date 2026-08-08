<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $table = 'provider';
    protected $primaryKey = 'id_provider';

    protected $fillable = [
        'id_user',
        'rating',
        'detail_provider',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'id_provider', 'id_provider');
    }

    public function negosiasi()
    {
        return $this->hasMany(Negosiasi::class, 'id_provider', 'id_provider');
    }

    // Hitung ulang rating rata-rata dari rating_review, dipanggil setelah ada review baru
    public function refreshRating(): void
    {
        $avg = RatingReview::whereHas('pesanan.negosiasi', function ($q) {
            $q->where('id_provider', $this->id_provider);
        })->avg('rate');

        $this->update(['rating' => round($avg ?? 0, 1)]);
    }
}
