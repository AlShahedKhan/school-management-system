<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageShowcase extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'title_bn',
        'image',
    ];

    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'bn') {
            return $this->title_bn
                ?: $this->title_en
                ?: $this->title
                ?: '';
        }

        return $this->title_en
            ?: $this->title
            ?: '';
    }
}
