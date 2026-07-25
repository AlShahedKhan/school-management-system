<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $blogs = Blog::query()
            ->with('translations')
            ->latest()
            ->paginate(10);

        return view('admin.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // create new blog page
        return view('admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('blogs', 'public');
            }
            DB::transaction(function () use ($data, $imagePath): void {
                $blog = Blog::create([
                    'slug' => $this->generateUniqueSlug(
                        $data['en']['title']
                    ),
                    'image' => $imagePath,
                    'status' => $data['status'],
                    'is_featured' => $data['is_featured'] ?? false,
                    'published_at' => $this->resolvePublishedAt($data),
                ]);
                foreach (['en', 'bn'] as $locale) {
                    $blog->translations()->create([
                        'locale' => $locale,
                        'title' => $data[$locale]['title'],
                        'description' => $data[$locale]['description'],
                        'seo_tags' => $data[$locale]['seo_tags'] ?? null,
                        'meta_title' => $data[$locale]['meta_title'] ?? null,
                        'meta_keywords' => $data[$locale]['meta_keywords'] ?? null,
                        'meta_description' => $data[$locale]['meta_description'] ?? null,
                    ]);
                }
            });

        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog): View
    {
        $blog->load('translations');

        return view('admin.blog.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog): View
    {
        $blog->load('translations');

        return view('admin.blog.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBlogRequest $request, Blog $blog): RedirectResponse
    {
        $data = $request->validated();

        $newImagePath = null;
        $oldImagePath = $blog->image;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')
                    ->store('blogs', 'public');
            }

            DB::transaction(
                function () use (
                    $blog,
                    $data,
                    $newImagePath
                ): void {
                    $blog->update([
                        'image' => $newImagePath ?? $blog->image,
                        'status' => $data['status'],
                        'is_featured' => $data['is_featured'] ?? false,
                        'published_at' => $this->resolvePublishedAt(
                            $data,
                            $blog
                        ),
                    ]);
                    foreach (['en', 'bn'] as $locale) {
                        $blog->translations()->updateOrCreate(
                            [
                                'locale' => $locale,
                            ],
                            [
                                'title' => $data[$locale]['title'],
                                'description' => $data[$locale]['description'],
                                'seo_tags' => $data[$locale]['seo_tags'] ?? null,
                                'meta_title' => $data[$locale]['meta_title'] ?? null,
                                'meta_keywords' => $data[$locale]['meta_keywords'] ?? null,
                                'meta_description' => $data[$locale]['meta_description'] ?? null,
                            ]
                        );
                    }
                }
            );
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }
        if (
            $newImagePath !== null
            && $oldImagePath !== null
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        if ($blog->image !== null) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog deleted successfully.');
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (
            Blog::withTrashed()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function resolvePublishedAt(
        array $data,
        ?Blog $blog = null
    ): mixed {
        if ($data['status'] !== 'published') {
            return null;
        }

        return $data['published_at']
            ?? $blog?->published_at
            ?? now();
    }
}
