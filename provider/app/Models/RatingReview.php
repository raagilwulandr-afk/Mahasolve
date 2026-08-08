<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model
{
    use HasFactory;

    protected $table = 'rating_review';

    protected $primaryKey = 'id_review';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_user',
        'rating',
        'review',
        'tanggal_review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'tanggal_review' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(
            Pesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function mahasiswa()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }
}
