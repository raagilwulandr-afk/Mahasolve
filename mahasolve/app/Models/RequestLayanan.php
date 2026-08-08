<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RequestLayanan extends Model
{
    protected $table = 'request_layanan';
    protected $primaryKey = 'id_request';

    protected $fillable = [
        'id_user',
        'detail_kebutuhan',
        'kategori',
        'harga_awal',
        'deadline',
        'tanggal_request',
        'status_request',
        'kriteria_output',
    ];

    protected $casts = [
        'deadline' => 'date',
        'tanggal_request' => 'datetime',
        'status_request' => RequestStatus::class,
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function negosiasi()
    {
        return $this->hasMany(Negosiasi::class, 'id_request', 'id_request');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status_request', RequestStatus::Open->value);
    }
}
