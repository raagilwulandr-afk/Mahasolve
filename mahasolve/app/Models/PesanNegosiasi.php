<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesanNegosiasi extends Model
{
    protected $table = 'pesan_negosiasi';
    protected $primaryKey = 'id_pesan';

    protected $fillable = [
        'id_negosiasi',
        'id_pengirim',
        'peran_pengirim',
        'pesan',
        'harga_tawaran',
    ];

    public function negosiasi()
    {
        return $this->belongsTo(Negosiasi::class, 'id_negosiasi', 'id_negosiasi');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'id_pengirim', 'id_user');
    }
}
