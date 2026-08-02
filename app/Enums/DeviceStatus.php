<?php

namespace App\Enums;

enum DeviceStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case HOLD = 'hold';

    /**
     * Get the display-friendly label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::HOLD => 'Hold',
        };
    }

    /**
     * Get the CSS class for the status badge.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'status-active',
            self::INACTIVE => 'status-inactive',
            self::HOLD => 'status-hold',
        };
    }

    /**
     * Get all possible status values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
