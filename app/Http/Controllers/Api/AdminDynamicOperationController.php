<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DynamicOperation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminDynamicOperationController extends Controller
{
    private function ensureSettings()
    {
        return DynamicOperation::firstOrCreate(
            ['id' => 1],
            ['brand_title' => 'Astha Academics']
        );
    }

    private function getSettings()
    {
        // Use find(1) to keep the global config centralized
        return DynamicOperation::find(1) ?? new DynamicOperation();
    }

    /**
     * Helper to delete the row if all monitored fields are empty
     */
    private function cleanupIfEmpty($settings)
    {
        if (!$settings->exists) return null;

        $fields = [
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

        foreach ($fields as $field) {
            if (!empty($settings->$field)) {
                return $settings; // Data exists, keep the row
            }
        }

        $settings->delete();
        return new DynamicOperation(); // Return empty object for UI consistency
    }

    public function index()
    {
        return response()->json($this->getSettings());
    }

    public function updateText(Request $request)
    {
        $settings = $this->ensureSettings();
        $allowed = ['brand_title', 'brand_description', 'promotion_text'];

        $field = $request->field;
        if (!in_array($field, $allowed)) {
            return response()->json(['message' => 'Invalid field selection'], 422);
        }

        $settings->$field = $request->value;
        $settings->save();

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }

    public function updateFooterContent(Request $request)
    {
        $validated = $request->validate([
            'brand_title_en' => ['nullable', 'string', 'max:255'],
            'brand_title_bn' => ['nullable', 'string', 'max:255'],
            'footer_description_en' => ['nullable', 'string', 'max:400'],
            'footer_description_bn' => ['nullable', 'string', 'max:400'],
            'footer_trust_badges_i18n' => ['nullable', 'array', 'max:6'],
            'footer_trust_badges_i18n.*.label_en' => ['nullable', 'string', 'max:40'],
            'footer_trust_badges_i18n.*.label_bn' => ['nullable', 'string', 'max:40'],
            'footer_trust_badges_i18n.*.style' => ['required', 'in:success,neutral,info'],
        ]);

        $settings = $this->ensureSettings();

        $brandTitleEn = array_key_exists('brand_title_en', $validated)
            ? trim((string) $validated['brand_title_en'])
            : trim((string) ($settings->brand_title_en ?: $settings->brand_title));
        $brandTitleBn = array_key_exists('brand_title_bn', $validated)
            ? trim((string) $validated['brand_title_bn'])
            : trim((string) $settings->brand_title_bn);
        $footerDescriptionEn = array_key_exists('footer_description_en', $validated)
            ? trim((string) $validated['footer_description_en'])
            : trim((string) ($settings->footer_description_en ?: $settings->footer_description));
        $footerDescriptionBn = array_key_exists('footer_description_bn', $validated)
            ? trim((string) $validated['footer_description_bn'])
            : trim((string) $settings->footer_description_bn);

        $badges = collect($validated['footer_trust_badges_i18n'] ?? [])
            ->map(function (array $badge) {
                return [
                    'label_en' => trim((string) ($badge['label_en'] ?? '')),
                    'label_bn' => trim((string) ($badge['label_bn'] ?? '')),
                    'style' => $badge['style'],
                ];
            })
            ->filter(fn (array $badge) => $badge['label_en'] !== '' || $badge['label_bn'] !== '')
            ->values()
            ->all();

        if (count($badges) > 6) {
            return response()->json(['message' => 'A maximum of 6 footer badges is allowed.'], 422);
        }

        $settings->brand_title_en = $brandTitleEn !== '' ? $brandTitleEn : null;
        $settings->brand_title_bn = $brandTitleBn !== '' ? $brandTitleBn : null;
        $settings->brand_title = $brandTitleEn !== '' ? $brandTitleEn : null;
        $settings->footer_description_en = $footerDescriptionEn !== '' ? $footerDescriptionEn : null;
        $settings->footer_description_bn = $footerDescriptionBn !== '' ? $footerDescriptionBn : null;
        $settings->footer_description = $footerDescriptionEn !== '' ? $footerDescriptionEn : null;
        $settings->footer_trust_badges_i18n = !empty($badges) ? $badges : null;
        $settings->footer_trust_badges = !empty($badges)
            ? collect($badges)->map(fn (array $badge) => [
                'label' => $badge['label_en'],
                'style' => $badge['style'],
            ])->values()->all()
            : null;
        $settings->save();

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }

    public function uploadLogo(Request $request)
    {
        $settings = $this->ensureSettings();
        $field = $request->field;
        $allowed = [
            'brand_logo',
            'brand_logo_light',
            'brand_logo_dark',
            'brand_favicon',
            'school_dashboard_logo',
            'brand_banner',
        ];

        if (!in_array($field, $allowed)) {
            return response()->json(['message' => 'Invalid asset field'], 422);
        }

        $rules = [
            'brand_logo' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'brand_logo_light' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'brand_logo_dark' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'brand_favicon' => 'required|file|mimes:png,ico,svg|max:1024',
            'school_dashboard_logo' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'brand_banner' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ];

        $validator = Validator::make($request->all(), [
            'image' => $rules[$field],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('dynamic', 'public');

            // Clean up old file to save storage
            if ($settings->$field) {
                Storage::disk('public')->delete($settings->$field);
            }

            $settings->$field = $path;
            $settings->save();
        }

        return response()->json($settings);
    }

    public function uploadSchoolBanners(Request $request)
    {
        $settings = $this->ensureSettings();
        $existing = $settings->school_dashboard_banners ?? [];

        // Strict Enforcement: Check limit before processing
        if (count($existing) >= 3) {
            return response()->json([
                'message' => 'Elite standard limit reached: Maximum 3 banners allowed.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (count($existing) >= 3) break;
                $existing[] = $file->store('dynamic', 'public');
            }
        }

        $settings->school_dashboard_banners = $existing;
        $settings->save();

        return response()->json($settings);
    }

    public function deleteImage(Request $request)
    {
        $settings = DynamicOperation::find(1);
        if (!$settings) return response()->json(new DynamicOperation());

        $field = $request->field;
        $allowed = [
            'brand_logo',
            'brand_logo_light',
            'brand_logo_dark',
            'brand_favicon',
            'school_dashboard_logo',
            'brand_banner',
        ];

        if (!in_array($field, $allowed)) {
            return response()->json(['message' => 'Invalid asset field'], 422);
        }

        if ($settings->$field && is_string($settings->$field)) {
            Storage::disk('public')->delete($settings->$field);
            $settings->$field = null;
            $settings->save();
        }

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }

    public function deleteSchoolBanner(Request $request)
    {
        $settings = DynamicOperation::find(1);
        if (!$settings) return response()->json(new DynamicOperation());

        $index = $request->index;
        $banners = $settings->school_dashboard_banners ?? [];

        if (isset($banners[$index])) {
            Storage::disk('public')->delete($banners[$index]);
            array_splice($banners, $index, 1);

            // Re-index to maintain clean JSON array in DB
            $settings->school_dashboard_banners = array_values($banners);
            $settings->save();
        }

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }
}
