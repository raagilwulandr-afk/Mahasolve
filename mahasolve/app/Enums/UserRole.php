<?php

namespace App\Enums;

enum UserRole: string
{
    case Mahasiswa = 'mahasiswa';
    case Provider = 'provider';

    public function label(): string
    {
        return match ($this) {
            self::Mahasiswa => 'Mahasiswa',
            self::Provider => 'Mitra Provider',
        };
    }
}
