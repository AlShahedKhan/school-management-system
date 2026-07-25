<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ALLOWED_BADGE_STYLES = [
        'success',
        'neutral',
        'info',
    ];

    public function up(): void
    {
        if (! $this->hasRequiredColumns()) {
            return;
        }

        DB::table('dynamic_operations')
            ->orderBy('id')
            ->get()
            ->each(function (object $record): void {
                $updates = [];

                $brandTitle = trim((string) $record->brand_title);
                if (blank($record->brand_title_en) && $brandTitle !== '') {
                    $updates['brand_title_en'] = $brandTitle;
                }

                $footerDescription = trim((string) $record->footer_description);
                if (blank($record->footer_description_en) && $footerDescription !== '') {
                    $updates['footer_description_en'] = $footerDescription;
                }

                if (blank($record->footer_trust_badges_i18n)) {
                    $badges = $this->legacyBadgesToI18n($record->footer_trust_badges);

                    if ($badges !== []) {
                        $updates['footer_trust_badges_i18n'] = json_encode($badges, JSON_UNESCAPED_UNICODE);
                    }
                }

                if ($updates !== []) {
                    DB::table('dynamic_operations')
                        ->where('id', $record->id)
                        ->update($updates);
                }
            });
    }

    public function down(): void
    {
        //
    }

    private function hasRequiredColumns(): bool
    {
        if (! Schema::hasTable('dynamic_operations')) {
            return false;
        }

        foreach ([
            'brand_title',
            'brand_title_en',
            'footer_description',
            'footer_description_en',
            'footer_trust_badges',
            'footer_trust_badges_i18n',
        ] as $column) {
            if (! Schema::hasColumn('dynamic_operations', $column)) {
                return false;
            }
        }

        return true;
    }

    private function legacyBadgesToI18n(mixed $legacyBadges): array
    {
        $badges = is_string($legacyBadges)
            ? json_decode($legacyBadges, true)
            : $legacyBadges;

        if (! is_array($badges)) {
            return [];
        }

        return collect($badges)
            ->filter(fn ($badge) => is_array($badge))
            ->map(function (array $badge): ?array {
                $label = trim((string) ($badge['label'] ?? ''));

                if ($label === '') {
                    return null;
                }

                $style = (string) ($badge['style'] ?? 'success');

                return [
                    'label_en' => $label,
                    'label_bn' => '',
                    'style' => in_array($style, self::ALLOWED_BADGE_STYLES, true)
                        ? $style
                        : 'success',
                ];
            })
            ->filter()
            ->take(6)
            ->values()
            ->all();
    }
};
