<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $primaryKey = 'id_layanan';

    public $timestamps = false;

    protected $fillable = [
        'id_provider',
        'id_user',
        'nama_layanan',
        'deskripsi',
        'kategori',
        'harga',
        'estimasi_pengerjaan',
        'status',
        'tanggal_dibuat',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tanggal_dibuat' => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(
            Provider::class,
            'id_provider',
            'id_provider'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

    public function requestLayanan()
    {
        return $this->hasMany(
            RequestLayanan::class,
            'id_layanan',
            'id_layanan'
        );
    }

    public function negosiasi()
    {
        return $this->hasMany(
            Negosiasi::class,
            'id_layanan',
            'id_layanan'
        );
    }

    public function pesanan()
    {
        return $this->hasMany(
            Pesanan::class,
            'id_layanan',
            'id_layanan'
        );
    }
    public function getRouteKeyName(): string
    {
        return 'id_layanan';
    }
}
