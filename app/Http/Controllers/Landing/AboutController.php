<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\AboutPerson;
use App\Support\AboutPageContentResolver;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(AboutPageContentResolver $resolver): View
    {
        return view('public.about', [
            'aboutPage' => $resolver->resolve(),
            'people' => AboutPerson::query()
                ->active()
                ->ordered()
                ->get(),
        ]);
    }

    public function showPerson(AboutPerson $aboutPerson): View
    {
        abort_unless($aboutPerson->is_active, 404);

        $relatedPeople = AboutPerson::query()
            ->active()
            ->whereKeyNot($aboutPerson->getKey())
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.about-person', compact('aboutPerson', 'relatedPeople'));
    }

    public function mission(AboutPageContentResolver $resolver): View
    {
        return view('public.about-mission', [
            'aboutPage' => $resolver->resolve(),
        ]);
    }

    public function vision(AboutPageContentResolver $resolver): View
    {
        return view('public.about-vision', [
            'aboutPage' => $resolver->resolve(),
        ]);
    }
}
