<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $blogs = Blog::query()
            ->published()
            ->with('translations')
            ->latest('published_at')
            ->paginate(9);

        return view('public.blog.index', compact('blogs'));
    }

    public function show(Blog $blog): View
    {
        abort_unless(
            $blog->status === 'published'
            && $blog->published_at !== null
            && $blog->published_at->lte(now()),
            404
        );

        $blog->load('translations');

        return view('public.blog.show', compact('blog'));
    }
}
