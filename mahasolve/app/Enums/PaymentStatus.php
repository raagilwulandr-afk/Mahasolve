<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Dikonfirmasi = 'dikonfirmasi';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Dikonfirmasi => 'Telah Dikonfirmasi',
            self::Gagal => 'Gagal / Ditolak',
        };
    }
}
