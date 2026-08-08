<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_provider',
        'id_layanan',
        'id_negosiasi',
        'harga_final',
        'tanggal_pesanan',
        'status_pesanan',
        'total_harga',
    ];

    protected $casts = [
        'harga_final' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'tanggal_pesanan' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
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

    public function layanan()
    {
        return $this->belongsTo(
            Layanan::class,
            'id_layanan',
            'id_layanan'
        );
    }

    public function negosiasi()
    {
        return $this->belongsTo(
            Negosiasi::class,
            'id_negosiasi',
            'id_negosiasi'
        );
    }

    public function detailPekerjaan()
    {
        return $this->hasOne(
            DetailPekerjaan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function tracking()
    {
        return $this->hasMany(
            TrackingPesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function trackingTerbaru()
    {
        return $this->hasOne(
            TrackingPesanan::class,
            'id_pesanan',
            'id_pesanan'
        )->latestOfMany('tanggal_update');
    }

    public function pembayaran()
    {
        return $this->hasOne(
            Pembayaran::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function review()
    {
        return $this->hasOne(
            RatingReview::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
    public function getRouteKeyName(): string
    {
        return 'id_pesanan';
    }
}
