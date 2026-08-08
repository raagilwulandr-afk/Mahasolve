<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negosiasi extends Model
{
    use HasFactory;

    protected $table = 'negosiasi';

    protected $primaryKey = 'id_negosiasi';

    public $timestamps = false;

    protected $fillable = [
        'id_matching',
        'id_request',
        'id_layanan',
        'id_provider',
        'penawaran_harga',
        'catatan',
        'status_negosiasi',
        'tanggal_negosiasi',
    ];

    protected $casts = [
        'penawaran_harga' => 'decimal:2',
        'tanggal_negosiasi' => 'datetime',
    ];

    public function matchingProvider()
    {
        return $this->belongsTo(
            MatchingProvider::class,
            'id_matching',
            'id_matching'
        );
    }

    public function requestLayanan()
    {
        return $this->belongsTo(
            RequestLayanan::class,
            'id_request',
            'id_request'
        );
    }

    public function layanan()
    {
        return $this->belongsTo(
            Layanan::class,
            'id_layanan',
            'id_layanan'
        );
    }

    public function provider()
    {
        return $this->belongsTo(
            Provider::class,
            'id_provider',
            'id_provider'
        );
    }

    public function pesanan()
    {
        return $this->hasOne(
            Pesanan::class,
            'id_negosiasi',
            'id_negosiasi'
        );
    }
    public function getRouteKeyName(): string
    {
        return 'id_negosiasi';
    }
}
