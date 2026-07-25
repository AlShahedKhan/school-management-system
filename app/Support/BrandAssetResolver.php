<?php

namespace App\Support;

use App\Models\DynamicOperation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BrandAssetResolver
{
    private const DEFAULT_BRAND_DESCRIPTION = 'Elite Multi-Tenant Saas Infrastructure For Modern Institutional Management.';
    private const DEFAULT_PROMOTION_TEXT = 'Promoted';
    private const DEFAULT_BRAND_TITLE_EN = 'Astha Academics';
    private const DEFAULT_BRAND_TITLE_BN = 'আস্থা একাডেমিক্স';
    private const DEFAULT_FOOTER_DESCRIPTION_EN = 'The complete school management platform trusted by institutions across Bangladesh. Simplify administration, empower teachers, and engage parents all in one place.';
    private const DEFAULT_FOOTER_DESCRIPTION_BN = 'বাংলাদেশের শিক্ষা প্রতিষ্ঠানগুলোর আস্থাভাজন পূর্ণাঙ্গ স্কুল ম্যানেজমেন্ট প্ল্যাটফর্ম। প্রশাসন সহজ করুন, শিক্ষকদের ক্ষমতায়ন করুন এবং অভিভাবকদের এক জায়গা থেকে সম্পৃক্ত রাখুন।';

    private const DEFAULT_FOOTER_BADGES_I18N = [
        ['label_en' => 'SSL', 'label_bn' => 'এসএসএল', 'style' => 'success'],
        ['label_en' => 'GDPR Compliant', 'label_bn' => 'জিডিপিআর কমপ্লায়েন্ট', 'style' => 'neutral'],
        ['label_en' => '99.9% Uptime', 'label_bn' => '৯৯.৯% আপটাইম', 'style' => 'info'],
    ];

    public function resolve(): array
    {
        $defaultLogoUrl = asset('images/logo.png');
        $defaultFaviconUrl = asset('images/logo.png');

        if (! Schema::hasTable('dynamic_operations')) {
            return $this->fallbackPayload($defaultLogoUrl, $defaultFaviconUrl);
        }

        $settings = DynamicOperation::find(1);

        $brandTitle = $this->resolveBrandTitle($settings);
        $lightLogoPath = $settings?->brand_logo_light ?: $settings?->brand_logo;
        $darkLogoPath = $settings?->brand_logo_dark ?: $lightLogoPath;
        $faviconPath = $settings?->brand_favicon;
        $bannerPath = $settings?->brand_banner;

        $logoLightUrl = $this->resolveStorageAsset($lightLogoPath, $defaultLogoUrl);
        $logoDarkUrl = $this->resolveStorageAsset($darkLogoPath, $logoLightUrl);
        $faviconUrl = $this->resolveStorageAsset($faviconPath, $defaultFaviconUrl);
        $bannerUrl = $this->resolveOptionalStorageAsset($bannerPath);

        return [
            'brandTitle' => $brandTitle,
            'brandDescription' => $this->translatedText(
                'public.brand.description',
                $this->resolveText($settings?->brand_description, self::DEFAULT_BRAND_DESCRIPTION)
            ),
            'promotionText' => $this->translatedText(
                'public.brand.promotion_text',
                $this->resolveText($settings?->promotion_text, self::DEFAULT_PROMOTION_TEXT)
            ),
            'logoAlt' => $brandTitle,
            'logoLightUrl' => $logoLightUrl,
            'logoDarkUrl' => $logoDarkUrl,
            'bannerUrl' => $bannerUrl,
            'faviconUrl' => $faviconUrl,
            'faviconType' => $this->detectMimeType($faviconPath),
            'footerDescription' => $this->resolveFooterDescription($settings),
            'footerTrustBadges' => $this->resolveFooterTrustBadges($settings),
        ];
    }

    private function fallbackPayload(string $defaultLogoUrl, string $defaultFaviconUrl): array
    {
        return [
            'brandTitle' => $this->defaultBrandTitle(),
            'brandDescription' => self::DEFAULT_BRAND_DESCRIPTION,
            'promotionText' => self::DEFAULT_PROMOTION_TEXT,
            'logoAlt' => $this->defaultBrandTitle(),
            'logoLightUrl' => $defaultLogoUrl,
            'logoDarkUrl' => $defaultLogoUrl,
            'bannerUrl' => null,
            'faviconUrl' => $defaultFaviconUrl,
            'faviconType' => 'image/png',
            'footerDescription' => $this->defaultFooterDescription(),
            'footerTrustBadges' => $this->defaultFooterBadges(),
        ];
    }

    private function resolveBrandTitle(?DynamicOperation $settings): string
    {
        $fallback = $this->resolveLocalizedText(
            $settings?->brand_title_en ?: $settings?->brand_title,
            $settings?->brand_title_bn,
            self::DEFAULT_BRAND_TITLE_EN,
            self::DEFAULT_BRAND_TITLE_BN
        );

        return $this->translatedText(
            'public.brand.title',
            $settings?->brand_title_en ?: $settings?->brand_title ?: self::DEFAULT_BRAND_TITLE_EN,
            $settings?->brand_title_bn ?: $fallback
        );
    }

    private function resolveFooterDescription(?DynamicOperation $settings): string
    {
        $fallback = $this->resolveLocalizedText(
            $settings?->footer_description_en ?: $settings?->footer_description,
            $settings?->footer_description_bn,
            self::DEFAULT_FOOTER_DESCRIPTION_EN,
            self::DEFAULT_FOOTER_DESCRIPTION_BN
        );

        return $this->translatedText(
            'public.footer.description',
            $settings?->footer_description_en ?: $settings?->footer_description ?: self::DEFAULT_FOOTER_DESCRIPTION_EN,
            $settings?->footer_description_bn ?: $fallback
        );
    }

    private function resolveLocalizedText(?string $english, ?string $bangla, string $fallbackEnglish, string $fallbackBangla): string
    {
        $english = $this->cleanText($english);
        $bangla = $this->cleanText($bangla);

        if (app()->getLocale() === 'bn') {
            return $bangla !== '' ? $bangla : ($english !== '' ? $english : $fallbackBangla);
        }

        return $english !== '' ? $english : ($bangla !== '' ? $bangla : $fallbackEnglish);
    }

    private function resolveText(?string $value, string $fallback): string
    {
        $value = $this->cleanText($value);

        return $value !== '' ? $value : $fallback;
    }

    private function resolveFooterTrustBadges(?DynamicOperation $settings): array
    {
        $badges = $settings?->footer_trust_badges_i18n;

        if (! is_array($badges) || empty($badges)) {
            $legacyBadges = $settings?->footer_trust_badges;

            if (is_array($legacyBadges) && ! empty($legacyBadges)) {
                $badges = collect($legacyBadges)
                    ->map(fn (array $badge) => [
                        'label_en' => trim((string) ($badge['label'] ?? '')),
                        'label_bn' => '',
                        'style' => (string) ($badge['style'] ?? 'success'),
                    ])
                    ->all();
            }
        }

        if (! is_array($badges) || empty($badges)) {
            return $this->defaultFooterBadges();
        }

        $normalized = collect($badges)
            ->map(function ($badge, int $index) {
                if (! is_array($badge)) {
                    return null;
                }

                $labelEn = $this->cleanText($badge['label_en'] ?? null);
                $labelBn = $this->cleanText($badge['label_bn'] ?? null);
                $style = (string) ($badge['style'] ?? '');

                if (($labelEn === '' && $labelBn === '') || ! in_array($style, ['success', 'neutral', 'info'], true)) {
                    return null;
                }

                return [
                    'label' => $this->translatedText(
                        "public.footer.badges.{$index}.label",
                        $labelEn !== '' ? $labelEn : $labelBn,
                        $labelBn !== '' ? $labelBn : $labelEn
                    ),
                    'style' => $style,
                ];
            })
            ->filter()
            ->take(6)
            ->values()
            ->all();

        return ! empty($normalized) ? $normalized : $this->defaultFooterBadges();
    }

    private function defaultBrandTitle(): string
    {
        return app()->getLocale() === 'bn'
            ? self::DEFAULT_BRAND_TITLE_BN
            : self::DEFAULT_BRAND_TITLE_EN;
    }

    private function defaultFooterDescription(): string
    {
        return app()->getLocale() === 'bn'
            ? self::DEFAULT_FOOTER_DESCRIPTION_BN
            : self::DEFAULT_FOOTER_DESCRIPTION_EN;
    }

    private function defaultFooterBadges(): array
    {
        return collect(self::DEFAULT_FOOTER_BADGES_I18N)
            ->map(fn (array $badge) => [
                'label' => app()->getLocale() === 'bn' ? $badge['label_bn'] : $badge['label_en'],
                'style' => $badge['style'],
            ])
            ->all();
    }

    private function resolveStorageAsset(?string $path, string $fallbackUrl): string
    {
        if (! $path) {
            return $fallbackUrl;
        }

        if (! Storage::disk('public')->exists($path)) {
            return $fallbackUrl;
        }

        return asset('storage/' . $path);
    }

    private function resolveOptionalStorageAsset(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . $path);
    }

    private function detectMimeType(?string $path): string
    {
        $extension = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));

        return match ($extension) {
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'image/png',
        };
    }

    private function cleanText(?string $value): string
    {
        $value = trim((string) $value);

        foreach (["\xC3\xA0\xC2\xA6", "\xC3\x83", "\xC3\x82", "\xEF\xBF\xBD"] as $marker) {
            if (str_contains($value, $marker)) {
                return '';
            }
        }

        return $value;
    }

    private function translatedText(string $key, ?string $englishDefault, ?string $banglaDefault = null): string
    {
        return app(PublicTranslationResolver::class)->getOrDefault($key, $englishDefault, $banglaDefault);
    }
}
