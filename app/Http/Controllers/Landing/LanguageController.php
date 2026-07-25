<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, config('public.locales', ['en', 'bn']), true), 404);

        session([config('public.locale_session_key', 'public_locale') => $locale]);

        return redirect()->back(fallback: route('home'));
    }
}
