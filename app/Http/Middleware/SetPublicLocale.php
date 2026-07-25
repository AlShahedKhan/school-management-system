<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('public.locales', ['en', 'bn']);
        $sessionKey = config('public.locale_session_key', 'public_locale');
        $defaultLocale = in_array(config('app.locale'), $supportedLocales, true)
            ? config('app.locale')
            : $supportedLocales[0];

        $locale = $request->session()->get($sessionKey, $defaultLocale);

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
