<?php

namespace App\Enums;

enum EmployeeStatusEnum: string
{
    case ACTIVE = 'active';
    case HOLD = 'hold';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::HOLD => 'Hold',
            self::INACTIVE => 'Inactive',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [
                $status->value => $status->label(),
            ])
            ->toArray();
    }
}