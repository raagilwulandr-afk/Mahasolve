<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingProvider extends Model
{
    use HasFactory;

    protected $table = 'matching_provider';

    protected $primaryKey = 'id_matching';

    public $timestamps = false;

    protected $fillable = [
        'id_request',
        'id_provider',
        'status_matching',
        'skor_matching',
        'tanggal_matching',
    ];

    protected $casts = [
        'skor_matching' => 'decimal:2',
        'tanggal_matching' => 'datetime',
    ];

    public function requestLayanan()
    {
        return $this->belongsTo(
            RequestLayanan::class,
            'id_request',
            'id_request'
        );
    }

    public function provider()
    {
        return $this->belongsTo(
            Provider::class,
            'id_provider',
            'id_provider'
        );
    }

    public function negosiasi()
    {
        return $this->hasMany(
            Negosiasi::class,
            'id_matching',
            'id_matching'
        );
    }
    public function getRouteKeyName(): string
    {
        return 'id_matching';
    }
}
