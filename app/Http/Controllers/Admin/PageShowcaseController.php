<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowcaseRequest;
use App\Models\PageShowcase;
use Illuminate\Support\Facades\Storage;

class PageShowcaseController extends Controller
{
    public function index()
    {
        $showcases = PageShowcase::query()
            ->select('id', 'title', 'title_en', 'title_bn', 'image', 'created_at')
            ->latest()
            ->paginate(10);

        return view('admin.showcase.index', compact('showcases'));
    }

    public function create()
    {
        return view('admin.showcase.create');
    }

    public function store(ShowcaseRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['title_en'] = trim($validatedData['title_en']);
        $validatedData['title_bn'] = isset($validatedData['title_bn'])
            ? trim((string) $validatedData['title_bn'])
            : null;
        $validatedData['title'] = $validatedData['title_en'];

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request
                ->file('image')
                ->store('showcase_images', 'public');
        }

        PageShowcase::create($validatedData);

        return redirect()
            ->route('admin.showcases.index')
            ->with('success', 'Showcase created successfully.');
    }

    public function show(PageShowcase $showcase)
    {
        return view('admin.showcase.show', compact('showcase'));
    }

    public function edit(PageShowcase $showcase)
    {
        return view('admin.showcase.edit', compact('showcase'));
    }

    public function update(
        ShowcaseRequest $request,
        PageShowcase $showcase
    ) {
        $validatedData = $request->validated();
        $validatedData['title_en'] = trim($validatedData['title_en']);
        $validatedData['title_bn'] = isset($validatedData['title_bn'])
            ? trim((string) $validatedData['title_bn'])
            : null;
        $validatedData['title'] = $validatedData['title_en'];

        if ($request->hasFile('image')) {
            if ($showcase->image) {
                Storage::disk('public')->delete($showcase->image);
            }

            $validatedData['image'] = $request
                ->file('image')
                ->store('showcase_images', 'public');
        }

        $showcase->update($validatedData);

        return redirect()
            ->route('admin.showcases.index')
            ->with('success', 'Showcase updated successfully.');
    }

    public function destroy(PageShowcase $showcase)
    {
        if ($showcase->image) {
            Storage::disk('public')->delete($showcase->image);
        }

        $showcase->delete();

        return redirect()
            ->route('admin.showcases.index')
            ->with('success', 'Showcase deleted successfully.');
    }
}
