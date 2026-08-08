<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'metode_bayar',
        'jumlah_bayar',
        'bukti_bayar',
        'status_bayar',
        'tanggal_bayar',
        'status',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
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
