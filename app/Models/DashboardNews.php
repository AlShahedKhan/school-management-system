<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DashboardNews extends Model
{
    protected $table = 'dashboard_news';

    protected $fillable = [
        'label',
        'message',
        'starts_at',
        'ends_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeDisplayOrder(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->latest();
    }
}
