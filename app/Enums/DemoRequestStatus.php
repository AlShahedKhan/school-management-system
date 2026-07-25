<?php

namespace App\Enums;

enum DemoRequestStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Scheduled = 'scheduled';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Scheduled => 'Scheduled',
            self::Closed => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::New => 'bg-blue-50 text-blue-700 border-blue-100',
            self::Contacted => 'bg-amber-50 text-amber-700 border-amber-100',
            self::Scheduled => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            self::Closed => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $status): string => $status->value,
            self::cases()
        );
    }
}
