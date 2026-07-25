<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {

        if (!Auth::check()) {
            return redirect('/');
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized Access');
        }

        return $next($request);
    }
}
