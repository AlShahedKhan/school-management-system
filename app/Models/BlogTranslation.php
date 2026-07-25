<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'locale',
        'title',
        'description',
        'seo_tags',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
