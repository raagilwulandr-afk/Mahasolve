<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $table = 'provider';

    protected $primaryKey = 'id_provider';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'rating',
        'detail_provider',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

    public function layanan()
    {
        return $this->hasMany(
            Layanan::class,
            'id_provider',
            'id_provider'
        );
    }

    public function matching()
    {
        return $this->hasMany(
            MatchingProvider::class,
            'id_provider',
            'id_provider'
        );
    }

    public function negosiasi()
    {
        return $this->hasMany(
            Negosiasi::class,
            'id_provider',
            'id_provider'
        );
    }

    public function pesanan()
    {
        return $this->hasMany(
            Pesanan::class,
            'id_provider',
            'id_provider'
        );
    }
}
