<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLayanan extends Model
{
    use HasFactory;

    protected $table = 'request_layanan';

    protected $primaryKey = 'id_request';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_layanan',
        'judul_request',
        'deskripsi_request',
        'kategori',
        'harga_awal',
        'deadline',
        'tanggal_request',
        'status_request',
        'kriteria_output',
    ];

    protected $casts = [
        'harga_awal' => 'decimal:2',
        'deadline' => 'date',
        'tanggal_request' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
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

    public function matchingProvider()
    {
        return $this->hasMany(
            MatchingProvider::class,
            'id_request',
            'id_request'
        );
    }

    public function negosiasi()
    {
        return $this->hasMany(
            Negosiasi::class,
            'id_request',
            'id_request'
        );
    }
}
