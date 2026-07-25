<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\View\View;

class FeatureController extends Controller
{
    private const FEATURES_PER_PAGE = 9;

    public function index(): View
    {
        $features = Feature::query()
            ->active()
            ->ordered()
            ->paginate(self::FEATURES_PER_PAGE)
            ->withQueryString();

        return view('public.features.index', compact('features'));
    }

    public function show(Feature $feature): View
    {
        abort_unless($feature->is_active, 404);

        $relatedFeatures = Feature::query()
            ->active()
            ->whereKeyNot($feature->getKey())
            ->ordered()
            ->limit(3)
            ->get();

        return view(
            'public.features.show',
            compact('feature', 'relatedFeatures')
        );
    }
}
