<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_negosiasi',
        'harga_final',
        'tanggal_pesanan',
        'status_pesanan',
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
    ];

    public function negosiasi()
    {
        return $this->belongsTo(Negosiasi::class, 'id_negosiasi', 'id_negosiasi');
    }

    public function detailPekerjaan()
    {
        return $this->hasMany(DetailPekerjaan::class, 'id_pesanan', 'id_pesanan');
    }

    public function trackingPesanan()
    {
        return $this->hasMany(TrackingPesanan::class, 'id_pesanan', 'id_pesanan')->orderBy('created_at');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function ratingReview()
    {
        return $this->hasOne(RatingReview::class, 'id_pesanan', 'id_pesanan');
    }

    // Helper: mahasiswa pemilik pesanan ini (lewat negosiasi -> request -> user)
    public function mahasiswa()
    {
        return $this->negosiasi->request->mahasiswa ?? null;
    }

    // Helper: provider pengerjaan pesanan ini
    public function provider()
    {
        return $this->negosiasi->provider ?? null;
    }

    // Boleh diisi review kalau sudah selesai & pembayaran dikonfirmasi
    public function bolehDireview(): bool
    {
        return $this->status_pesanan === 'selesai'
            && $this->pembayaran
            && $this->pembayaran->status_bayar === 'dikonfirmasi'
            && !$this->ratingReview;
    }
}
