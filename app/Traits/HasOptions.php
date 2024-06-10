<?php

namespace App\Traits;

/**
 * @method static cases()
 */
trait HasOptions
{
    public static function select_options(): array
    {
        return collect(self::cases())->map(fn($status) => [
            'name' => $status->label(),
            'value' => $status->value,
        ])->pluck('name', 'value')->toArray();
    }

    public static function select_colors(): array
    {
        return collect(self::cases())->map(fn($status) => [
            'color' => $status->color(),
            'value' => $status->value,
        ])->pluck('value', 'color')->toArray();
    }
}
