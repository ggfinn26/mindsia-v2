<?php

namespace App\Enums\Member;

enum ActivationStatus: string
{
    case Pending = 'pending';
    case Aktif = 'aktif';
    case Ditangguhkan = 'ditangguhkan';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Aktivasi',
            self::Aktif => 'Aktif',
            self::Ditangguhkan => 'Ditangguhkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Aktif => 'bg-green-100 text-green-800',
            self::Ditangguhkan => 'bg-red-100 text-red-800',
        };
    }
}
