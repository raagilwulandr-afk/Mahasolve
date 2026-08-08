<?php

namespace App\Enums;

enum NegotiationStatus: string
{
    case Pending = 'pending';
    case DitawarUlang = 'ditawar_ulang';
    case Disepakati = 'disepakati';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Respon',
            self::DitawarUlang => 'Penawaran Balik',
            self::Disepakati => 'Disepakati',
            self::Ditolak => 'Ditolak',
        };
    }
}
