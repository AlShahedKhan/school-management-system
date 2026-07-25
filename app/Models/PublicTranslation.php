<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicTranslation extends Model
{
    protected $fillable = [
        'key',
        'group',
        'en',
        'bn',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
