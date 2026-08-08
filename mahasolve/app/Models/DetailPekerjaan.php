<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPekerjaan extends Model
{
    protected $table = 'detail_pekerjaan';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pesanan',
        'dokumen',
        'instruksi_pengerjaan',
        'referensi',
        'format_hasil',
        'tanggal_upload',
        'status',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
