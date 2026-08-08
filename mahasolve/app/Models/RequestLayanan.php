<?php

namespace App\Models;

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
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function negosiasi()
    {
        return $this->hasMany(Negosiasi::class, 'id_request', 'id_request');
    }
}
