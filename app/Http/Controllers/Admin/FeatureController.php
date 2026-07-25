<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureRequest;
use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureController extends Controller
{
    public function index(): View
    {
        $features = Feature::query()
            ->ordered()
            ->paginate(12);

        return view('admin.feature.index', compact('features'));
    }

    public function create(): View
    {
        return view('admin.feature.create');
    }

    public function store(FeatureRequest $request): RedirectResponse
    {
        Feature::create($request->validated());

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'Feature created successfully.');
    }

    public function edit(Feature $feature): View
    {
        return view('admin.feature.edit', compact('feature'));
    }

    public function update(FeatureRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'Feature updated successfully.');
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'Feature deleted successfully.');
    }
}
