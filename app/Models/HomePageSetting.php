<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'hero_title_line_1',
        'hero_title_line_1_en',
        'hero_title_line_1_bn',
        'hero_highlight_text',
        'hero_highlight_text_en',
        'hero_highlight_text_bn',
        'hero_title_suffix',
        'hero_title_suffix_en',
        'hero_title_suffix_bn',
        'hero_accent_color',
        'hero_accent_soft_color',
        'hero_primary_button_color',
        'hero_primary_button_hover_color',
        'hero_description',
        'hero_description_en',
        'hero_description_bn',
        'hero_primary_cta_label',
        'hero_primary_cta_url',
        'hero_secondary_cta_label',
        'hero_secondary_cta_url',
        'hero_dashboard_label',
        'hero_school_name',
        'hero_attendance_label',
        'hero_attendance_value',
        'hero_activity_title',
        'hero_status_badge',
        'hero_status_badge_style',
        'hero_metric_cards',
        'hero_activity_items',
        'stats_items',
        'stats_items_en',
        'stats_items_bn',
        'intro_eyebrow',
        'intro_eyebrow_en',
        'intro_eyebrow_bn',
        'intro_title',
        'intro_title_en',
        'intro_title_bn',
        'intro_description',
        'intro_description_en',
        'intro_description_bn',
    ];

    protected $casts = [
        'hero_metric_cards' => 'array',
        'hero_activity_items' => 'array',
        'stats_items' => 'array',
        'stats_items_en' => 'array',
        'stats_items_bn' => 'array',
    ];
}
