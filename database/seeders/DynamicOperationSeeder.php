<?php

namespace Database\Seeders;

use App\Models\DynamicOperation;
use Illuminate\Database\Seeder;

class DynamicOperationSeeder extends Seeder
{
    public function run(): void
    {
        $settings = DynamicOperation::firstOrNew(['id' => 1]);

        $brandTitleEn = 'Astha Academics';
        $brandTitleBn = 'আস্থা একাডেমিক্স';
        $footerDescriptionEn = 'The complete school management platform trusted by institutions across Bangladesh. Simplify administration, empower teachers, and engage parents all in one place.';
        $footerDescriptionBn = 'বাংলাদেশের শিক্ষা প্রতিষ্ঠানগুলোর আস্থাভাজন পূর্ণাঙ্গ স্কুল ম্যানেজমেন্ট প্ল্যাটফর্ম। প্রশাসন সহজ করুন, শিক্ষকদের ক্ষমতায়ন করুন এবং অভিভাবকদের এক জায়গা থেকে সম্পৃক্ত রাখুন।';
        $footerTrustBadgesI18n = [
            ['label_en' => 'SSL', 'label_bn' => 'এসএসএল', 'style' => 'success'],
            ['label_en' => 'GDPR Compliant', 'label_bn' => 'জিডিপিআর কমপ্লায়েন্ট', 'style' => 'neutral'],
            ['label_en' => '99.9% Uptime', 'label_bn' => '৯৯.৯% আপটাইম', 'style' => 'info'],
        ];

        if (blank($settings->brand_title)) {
            $settings->brand_title = $brandTitleEn;
        }

        if (blank($settings->brand_title_en)) {
            $settings->brand_title_en = $brandTitleEn;
        }

        if (blank($settings->brand_title_bn) || $this->hasBrokenEncoding($settings->brand_title_bn)) {
            $settings->brand_title_bn = $brandTitleBn;
        }

        if (blank($settings->brand_description)) {
            $settings->brand_description = 'The complete school management platform trusted by institutions across Bangladesh.';
        }

        if (blank($settings->promotion_text)) {
            $settings->promotion_text = 'Simplify administration, empower teachers, and engage parents all in one place.';
        }

        if (blank($settings->footer_description)) {
            $settings->footer_description = $footerDescriptionEn;
        }

        if (blank($settings->footer_description_en)) {
            $settings->footer_description_en = $footerDescriptionEn;
        }

        if (blank($settings->footer_description_bn) || $this->hasBrokenEncoding($settings->footer_description_bn)) {
            $settings->footer_description_bn = $footerDescriptionBn;
        }

        if (empty($settings->footer_trust_badges)) {
            $settings->footer_trust_badges = collect($footerTrustBadgesI18n)
                ->map(fn (array $badge) => [
                    'label' => $badge['label_en'],
                    'style' => $badge['style'],
                ])
                ->all();
        }

        if (empty($settings->footer_trust_badges_i18n) || $this->hasBrokenBadgeEncoding($settings->footer_trust_badges_i18n)) {
            $settings->footer_trust_badges_i18n = $footerTrustBadgesI18n;
        }

        $settings->save();
    }

    private function hasBrokenEncoding(?string $value): bool
    {
        foreach (["\xC3\xA0\xC2\xA6", "\xC3\x83", "\xC3\x82", "\xEF\xBF\xBD"] as $marker) {
            if (str_contains((string) $value, $marker)) {
                return true;
            }
        }

        return false;
    }

    private function hasBrokenBadgeEncoding(mixed $badges): bool
    {
        if (! is_array($badges)) {
            return false;
        }

        return collect($badges)
            ->contains(function ($badge): bool {
                if (! is_array($badge)) {
                    return false;
                }

                return $this->hasBrokenEncoding($badge['label_bn'] ?? null)
                    || $this->hasBrokenEncoding($badge['label_en'] ?? null);
            });
    }
}
