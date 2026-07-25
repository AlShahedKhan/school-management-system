<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Feature;
use App\Models\Package;
use App\Models\PageShowcase;
use App\Support\HomePageContentResolver;

class HomeController extends Controller
{
    private const HOME_FEATURE_LIMIT = 6;

    public function index(HomePageContentResolver $resolver)
    {
        $blogs = Blog::query()
            ->published()
            ->with('translations')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.home', [
            'homePage' => $resolver->resolve(),
            'features' => Feature::query()
                ->active()
                ->ordered()
                ->limit(self::HOME_FEATURE_LIMIT)
                ->get(),
            'packages' => Package::query()
                ->active()
                ->ordered()
                ->get(),
            'showcases' => PageShowcase::query()
                ->latest()
                ->get(),
            'blogs' => $blogs,
        ]);
    }
}
