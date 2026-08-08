<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MenungguPengerjaan = 'menunggu_pengerjaan';
    case Dikerjakan = 'dikerjakan';
    case Revisi = 'revisi';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MenungguPengerjaan => 'Menunggu Pengerjaan',
            self::Dikerjakan => 'Sedang Dikerjakan',
            self::Revisi => 'Dalam Revisi',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Selesai, self::Dibatalkan], true);
    }
}
