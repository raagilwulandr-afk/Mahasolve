<?php

namespace App\Enums;

enum ServiceCategory: string
{
    case AntarJemput = 'Antar Jemput';
    case PrintFotokopi = 'Print & Fotokopi';
    case Bimbingan = 'Bimbingan';
    case DesainEditing = 'Desain & Editing';
    case TitipMakan = 'Titip Makan';
    case TitipBeli = 'Titip Beli';

    public function color(): string
    {
        return match ($this) {
            self::AntarJemput => '#4F46E5',
            self::PrintFotokopi => '#14B8A6',
            self::Bimbingan => '#F59E0B',
            self::DesainEditing => '#EC4899',
            self::TitipMakan => '#EF4444',
            self::TitipBeli => '#8B5CF6',
        };
    }

    public function iconSvg(): string
    {
        return match ($this) {
            self::AntarJemput => '<path d="M4 16l4-5 3 3 5-6 4 5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
            self::PrintFotokopi => '<rect x="5" y="9" width="14" height="8" rx="1.5" stroke="#fff" stroke-width="2"/><path d="M7 9V4h10v5M7 17v3h10v-3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>',
            self::Bimbingan => '<path d="M4 6l8-3 8 3-8 3-8-3z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M6 10v6c0 1 2.7 2 6 2s6-1 6-2v-6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>',
            self::DesainEditing => '<path d="M14 4l4 4-9 9-4.5.5.5-4.5L14 4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>',
            self::TitipMakan => '<path d="M6 3v7a2 2 0 002 2v9M6 3v5M8 3v5M10 3v5M10 3v18M18 3c-2 1-3 3-3 6s1 4 3 5v9" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
            self::TitipBeli => '<path d="M6 8h12l-1 12H7L6 8z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M9 8V6a3 3 0 016 0v2" stroke="#fff" stroke-width="2"/>',
        };
    }

    public static function allFormatted(): array
    {
        return array_map(fn (self $cat) => [
            'nama' => $cat->value,
            'warna' => $cat->color(),
            'icon' => $cat->iconSvg(),
        ], self::cases());
    }
}
