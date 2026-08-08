<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'detail_pekerjaan';

    protected $primaryKey = 'id_detail';

    public $timestamps = false;

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
        return $this->belongsTo(
            Pesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}
