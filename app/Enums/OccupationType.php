<?php


namespace App\Enums;

use App\Traits\HasOptions;

enum OccupationType: int
{
    use HasOptions;

    case Residential = 1;
    case Commercial = 2;

    public function label(): string
    {
        return match ($this) {
            self::Residential => 'المنتجات',
            self::Commercial => 'الطلبات',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Residential => 'info',
            self::Commercial => 'warning',
        };
    }
}
