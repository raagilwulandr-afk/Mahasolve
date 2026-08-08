<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model
{
    protected $table = 'rating_review';
    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_pesanan',
        'review',
        'rate',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
