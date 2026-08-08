<?php

namespace App\Models;

use App\Enums\NegotiationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Negosiasi extends Model
{
    protected $table = 'negosiasi';
    protected $primaryKey = 'id_negosiasi';

    protected $fillable = [
        'id_request',
        'id_provider',
        'harga_tawaran',
        'detail_negosiasi',
        'dibuat_oleh',
        'status_negosiasi',
    ];

    protected $casts = [
        'status_negosiasi' => NegotiationStatus::class,
    ];

    public function request()
    {
        return $this->belongsTo(RequestLayanan::class, 'id_request', 'id_request');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'id_provider', 'id_provider');
    }

    public function pesanan()
    {
        return $this->hasOne(Pesanan::class, 'id_negosiasi', 'id_negosiasi');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status_negosiasi', NegotiationStatus::Pending->value);
    }

    public function scopeThread(Builder $query, int $requestId, int $providerId): Builder
    {
        return $query->where('id_request', $requestId)
            ->where('id_provider', $providerId)
            ->orderBy('created_at', 'asc');
    }
}
