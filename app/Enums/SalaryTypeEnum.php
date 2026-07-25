<?php

namespace App\Enums;

enum SalaryTypeEnum: string
{
    case PAID = 'paid';
    case PARTIAL_PAID = 'partial_paid';
    case DUE_PAID = 'due_paid';

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::PARTIAL_PAID => 'Partial Paid',
            self::DUE_PAID => 'Due Paid',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [
                $type->value => $type->label(),
            ])
            ->toArray();
    }
}