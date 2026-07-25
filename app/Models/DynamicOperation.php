<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'brand_title',
        'brand_title_en',
        'brand_title_bn',
        'brand_description',
        'promotion_text',
        'footer_description',
        'footer_description_en',
        'footer_description_bn',
        'footer_trust_badges',
        'footer_trust_badges_i18n',
        'brand_logo',
        'brand_logo_light',
        'brand_logo_dark',
        'brand_favicon',
        'school_dashboard_logo',
        'brand_banner',
        'school_dashboard_banners'
    ];

    protected $casts = [
        'footer_trust_badges' => 'array',
        'footer_trust_badges_i18n' => 'array',
        'school_dashboard_banners' => 'array'
    ];
}
