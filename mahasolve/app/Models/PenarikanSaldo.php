<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenarikanSaldo extends Model
{
    protected $table = 'penarikan_saldo';
    protected $primaryKey = 'id_penarikan';

    protected $fillable = [
        'id_provider',
        'jumlah',
        'metode',
        'no_rekening',
        'status',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'id_provider', 'id_provider');
    }
}
