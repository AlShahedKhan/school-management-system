<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPageSetting extends Model
{
    protected $fillable = [
        'id',
        'page_eyebrow_en',
        'page_eyebrow_bn',
        'page_title_en',
        'page_title_bn',
        'page_intro_en',
        'page_intro_bn',
        'mission_title_en',
        'mission_title_bn',
        'mission_summary_en',
        'mission_summary_bn',
        'mission_details_en',
        'mission_details_bn',
        'vision_title_en',
        'vision_title_bn',
        'vision_summary_en',
        'vision_summary_bn',
        'vision_details_en',
        'vision_details_bn',
    ];
}
