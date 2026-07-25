<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'icon',
        'title_en',
        'title_bn',
        'description_en',
        'description_bn',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
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

    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->title_bn ?: $this->title_en)
            : $this->title_en;
    }

    public function localizedDescription(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn'
            ? ($this->description_bn ?: $this->description_en)
            : $this->description_en;
    }
}
