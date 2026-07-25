<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutPersonRequest;
use App\Models\AboutPerson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AboutPersonController extends Controller
{
    public function index(): View
    {
        $people = AboutPerson::query()
            ->ordered()
            ->paginate(12);

        return view('admin.about.people.index', compact('people'));
    }

    public function create(): View
    {
        return view('admin.about.people.create');
    }

    public function store(AboutPersonRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $request->file('image')->store('about_people', 'public');

        AboutPerson::create($validated);

        return redirect()
            ->route('admin.about.people.index')
            ->with('success', 'About profile created successfully.');
    }

    public function edit(AboutPerson $aboutPerson): View
    {
        return view('admin.about.people.edit', compact('aboutPerson'));
    }

    public function update(AboutPersonRequest $request, AboutPerson $aboutPerson): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($aboutPerson->image) {
                Storage::disk('public')->delete($aboutPerson->image);
            }

            $validated['image'] = $request->file('image')->store('about_people', 'public');
        }

        $aboutPerson->update($validated);

        return redirect()
            ->route('admin.about.people.index')
            ->with('success', 'About profile updated successfully.');
    }

    public function destroy(AboutPerson $aboutPerson): RedirectResponse
    {
        if ($aboutPerson->image) {
            Storage::disk('public')->delete($aboutPerson->image);
        }

        $aboutPerson->delete();

        return redirect()
            ->route('admin.about.people.index')
            ->with('success', 'About profile deleted successfully.');
    }
}
