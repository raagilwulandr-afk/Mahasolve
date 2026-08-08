<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingPesanan extends Model
{
    use HasFactory;

    protected $table = 'tracking_pesanan';

    protected $primaryKey = 'id_tracking';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'status_pengerjaan',
        'deskripsi',
        'file_progress',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
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
