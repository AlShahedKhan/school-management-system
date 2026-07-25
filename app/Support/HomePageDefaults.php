<?php

namespace App\Support;

class HomePageDefaults
{
    public const TONES = ['primary', 'success', 'warning', 'info'];
    public const BADGE_STYLES = ['success', 'neutral', 'info'];
    public const ICONS = [
        'layout-dashboard',
        'users',
        'graduation-cap',
        'credit-card',
        'clipboard-list',
        'shield-check',
        'calendar-check',
        'messages-square',
    ];

    public static function seedAttributes(): array
    {
        $hero = self::heroContentDefaults();
        $stats = self::statsContentDefaults();
        $intro = self::introContentDefaults();

        return [
            'hero_title_line_1' => $hero['en']['title_line_1'],
            'hero_title_line_1_en' => $hero['en']['title_line_1'],
            'hero_title_line_1_bn' => $hero['bn']['title_line_1'],
            'hero_highlight_text' => $hero['en']['highlight_text'],
            'hero_highlight_text_en' => $hero['en']['highlight_text'],
            'hero_highlight_text_bn' => $hero['bn']['highlight_text'],
            'hero_title_suffix' => $hero['en']['title_suffix'],
            'hero_title_suffix_en' => $hero['en']['title_suffix'],
            'hero_title_suffix_bn' => $hero['bn']['title_suffix'],
            'hero_accent_color' => '#2563EB',
            'hero_accent_soft_color' => '#DBEAFE',
            'hero_primary_button_color' => '#2563EB',
            'hero_primary_button_hover_color' => '#1D4ED8',
            'hero_description' => $hero['en']['description'],
            'hero_description_en' => $hero['en']['description'],
            'hero_description_bn' => $hero['bn']['description'],
            'hero_primary_cta_label' => 'Admin',
            'hero_primary_cta_url' => '/login',
            'hero_secondary_cta_label' => 'Login',
            'hero_secondary_cta_url' => '/login',
            'hero_dashboard_label' => 'Dashboard',
            'hero_school_name' => 'Dhaka Model School',
            'hero_attendance_label' => "Today's Attendance",
            'hero_attendance_value' => '91%',
            'hero_activity_title' => 'Recent Activity',
            'hero_status_badge' => 'Live & Secure',
            'hero_status_badge_style' => 'success',
            'hero_metric_cards' => self::defaultHeroMetricCards(),
            'hero_activity_items' => self::defaultHeroActivityItems(),
            'stats_items' => $stats['en'],
            'stats_items_en' => $stats['en'],
            'stats_items_bn' => $stats['bn'],
            'intro_eyebrow' => $intro['en']['eyebrow'],
            'intro_eyebrow_en' => $intro['en']['eyebrow'],
            'intro_eyebrow_bn' => $intro['bn']['eyebrow'],
            'intro_title' => $intro['en']['title'],
            'intro_title_en' => $intro['en']['title'],
            'intro_title_bn' => $intro['bn']['title'],
            'intro_description' => $intro['en']['description'],
            'intro_description_en' => $intro['en']['description'],
            'intro_description_bn' => $intro['bn']['description'],
        ];
    }

    public static function heroContentDefaults(): array
    {
        return [
            'en' => [
                'title_line_1' => 'Manage Your',
                'highlight_text' => 'School',
                'title_suffix' => 'Smarter, Not Harder',
                'description' => 'Astha Academics is a complete school management platform designed to simplify and digitalize all academic operations in one powerful system.',
            ],
            'bn' => [
                'title_line_1' => 'আপনার',
                'highlight_text' => 'স্কুল',
                'title_suffix' => 'আরও স্মার্টভাবে পরিচালনা করুন, কঠিনভাবে নয়',
                'description' => 'আস্থা একাডেমিকস একটি পূর্ণাঙ্গ স্কুল ম্যানেজমেন্ট প্ল্যাটফর্ম, যা সব একাডেমিক কার্যক্রমকে সহজ, ডিজিটাল এবং এক শক্তিশালী সিস্টেমে পরিচালিত করতে সাহায্য করে।',
            ],
        ];
    }

    public static function statsContentDefaults(): array
    {
        return [
            'en' => [
                ['value' => '2K+', 'label' => 'Schools Registered'],
                ['value' => '2L+', 'label' => 'Students Managed'],
                ['value' => '10K+', 'label' => 'Teachers Onboarded'],
                ['value' => '99.9%', 'label' => 'Uptime Guaranteed'],
            ],
            'bn' => [
                ['value' => '২ হাজার+', 'label' => 'নিবন্ধিত স্কুল'],
                ['value' => '২ লক্ষ+', 'label' => 'পরিচালিত শিক্ষার্থী'],
                ['value' => '১০ হাজার+', 'label' => 'অনবোর্ডেড শিক্ষক'],
                ['value' => '৯৯.৯%', 'label' => 'আপটাইম নিশ্চিত'],
            ],
        ];
    }

    public static function introContentDefaults(): array
    {
        return [
            'en' => [
                'eyebrow' => 'Everything You Need',
                'title' => 'One platform, every school need',
                'description' => 'Stop juggling spreadsheets and paper registers. Astha Academics brings every part of your school into a single, easy-to-use system.',
            ],
            'bn' => [
                'eyebrow' => 'আপনার প্রয়োজনের সবকিছু',
                'title' => 'এক প্ল্যাটফর্মে, প্রতিটি স্কুলের প্রয়োজনীয় সব কিছু',
                'description' => 'স্প্রেডশিট আর কাগজের রেজিস্টার নিয়ে ঝামেলা বন্ধ করুন। আস্থা একাডেমিকস আপনার স্কুলের প্রতিটি কাজকে এক সহজ ও ব্যবহারযোগ্য সিস্টেমে নিয়ে আসে।',
            ],
        ];
    }

    public static function defaultHeroMetricCards(): array
    {
        return [
            ['label' => 'Students', 'value' => '842', 'tone' => 'primary'],
            ['label' => 'Teachers', 'value' => '48', 'tone' => 'success'],
            ['label' => 'Fees Due', 'value' => 'BDT 1.2L', 'tone' => 'warning'],
        ];
    }

    public static function defaultHeroActivityItems(): array
    {
        return [
            ['text' => 'Result published - Class 9', 'meta' => '2m ago', 'tone' => 'primary'],
            ['text' => 'Fee collected - Rahim Ahmed', 'meta' => '15m ago', 'tone' => 'success'],
            ['text' => 'Absent alert sent - 12 parents', 'meta' => '1h ago', 'tone' => 'warning'],
        ];
    }

    public static function defaultStatsItems(): array
    {
        return self::statsContentDefaults()['en'];
    }

    public static function defaultFeatures(): array
    {
        return [
            [
                'icon' => 'layout-dashboard',
                'title_en' => 'Unified Dashboard',
                'title_bn' => 'একীভূত ড্যাশবোর্ড',
                'description_en' => 'See attendance, collections, announcements, and daily activity from one calm workspace.',
                'description_bn' => 'উপস্থিতি, কালেকশন, ঘোষণা এবং দৈনন্দিন কার্যক্রম এক জায়গা থেকে পরিষ্কারভাবে দেখুন।',
            ],
            [
                'icon' => 'users',
                'title_en' => 'Student & Teacher Management',
                'title_bn' => 'শিক্ষার্থী ও শিক্ষক ব্যবস্থাপনা',
                'description_en' => 'Manage profiles, classes, sections, and records without scattered spreadsheets.',
                'description_bn' => 'ছড়িয়ে থাকা স্প্রেডশিট ছাড়াই প্রোফাইল, ক্লাস, সেকশন এবং রেকর্ড পরিচালনা করুন।',
            ],
            [
                'icon' => 'credit-card',
                'title_en' => 'Fee Collection',
                'title_bn' => 'ফি সংগ্রহ',
                'description_en' => 'Track payable amounts, collections, dues, and receipts with clear operational visibility.',
                'description_bn' => 'প্রাপ্য ফি, আদায়, বকেয়া এবং রসিদ পরিষ্কারভাবে ট্র্যাক করুন।',
            ],
            [
                'icon' => 'clipboard-list',
                'title_en' => 'Attendance & Results',
                'title_bn' => 'উপস্থিতি ও ফলাফল',
                'description_en' => 'Handle attendance workflows, exam publishing, and academic updates in one system.',
                'description_bn' => 'একই সিস্টেমে উপস্থিতি, পরীক্ষা প্রকাশ এবং একাডেমিক আপডেট পরিচালনা করুন।',
            ],
            [
                'icon' => 'calendar-check',
                'title_en' => 'Routine & Notices',
                'title_bn' => 'রুটিন ও নোটিশ',
                'description_en' => 'Keep schedules, announcements, and school communication organized and easy to find.',
                'description_bn' => 'রুটিন, ঘোষণা এবং স্কুল যোগাযোগ গুছিয়ে ও সহজে খুঁজে পাওয়ার মতো রাখুন।',
            ],
            [
                'icon' => 'shield-check',
                'title_en' => 'Reliable Operations',
                'title_bn' => 'নির্ভরযোগ্য পরিচালনা',
                'description_en' => 'Run on secure, structured workflows designed for repeatable school administration tasks.',
                'description_bn' => 'নিরাপদ ও গঠিত ওয়ার্কফ্লোর মাধ্যমে পুনরাবৃত্ত স্কুল প্রশাসনিক কাজ পরিচালনা করুন।',
            ],
        ];
    }
}
