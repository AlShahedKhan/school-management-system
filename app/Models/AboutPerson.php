<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AboutPerson extends Model
{
    protected $fillable = [
        'image',
        'slug',
        'name_en',
        'name_bn',
        'designation_en',
        'designation_bn',
        'summary_en',
        'summary_bn',
        'details_en',
        'details_bn',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->name_bn ?: $this->name_en)
            : $this->name_en;
    }

    public function localizedDesignation(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->designation_bn ?: $this->designation_en)
            : $this->designation_en;
    }

    public function localizedSummary(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->summary_bn ?: $this->summary_en)
            : $this->summary_en;
    }

    public function localizedDetails(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->details_bn ?: $this->details_en)
            : $this->details_en;
    }
}
