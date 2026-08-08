<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'status_pesanan' => OrderStatus::class,
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

    public function mahasiswa()
    {
        return $this->negosiasi->request->mahasiswa ?? null;
    }

    public function provider()
    {
        return $this->negosiasi->provider ?? null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status_pesanan', [
            OrderStatus::MenungguPengerjaan->value,
            OrderStatus::Dikerjakan->value,
            OrderStatus::Revisi->value,
        ]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status_pesanan', OrderStatus::Selesai->value);
    }

    public function scopeForStudent(Builder $query, int $userId): Builder
    {
        return $query->whereHas('negosiasi.request', fn ($q) => $q->where('id_user', $userId));
    }

    public function scopeForProvider(Builder $query, int $providerId): Builder
    {
        return $query->whereHas('negosiasi', fn ($q) => $q->where('id_provider', $providerId));
    }

    public function bolehDireview(): bool
    {
        $statusVal = is_object($this->status_pesanan) ? $this->status_pesanan->value : $this->status_pesanan;
        $statusBayarVal = $this->pembayaran ? (is_object($this->pembayaran->status_bayar) ? $this->pembayaran->status_bayar->value : $this->pembayaran->status_bayar) : null;

        return $statusVal === 'selesai'
            && $this->pembayaran
            && $statusBayarVal === 'dikonfirmasi'
            && !$this->ratingReview;
    }
}
