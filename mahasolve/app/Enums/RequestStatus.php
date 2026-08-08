<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Open = 'open';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Terbuka (Open)',
            self::Diproses => 'Sedang Diproses',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}
