<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingPesanan extends Model
{
    protected $table = 'tracking_pesanan';
    protected $primaryKey = 'id_tracking';

    protected $fillable = [
        'id_pesanan',
        'status_pengerjaan',
        'file_progress',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
