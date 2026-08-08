<?php

namespace App\Models;

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
}
