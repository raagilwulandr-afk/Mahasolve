<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'id_provider',
        'nama_layanan',
        'kategori',
        'deskripsi',
        'harga',
        'estimasi_pengerjaan',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'id_provider', 'id_provider');
    }
}
