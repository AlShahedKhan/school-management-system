<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Symfony\Component\HttpFoundation\Response;

class CheckTeacherDefaultPassword
{

    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {

            $user = Auth::user();

            if ($user->role === 'teacher') {

                if (Hash::check('00000000', $user->password)) {

                    if (!$request->is('teacher/change-password') && !$request->is('api/teacher/change-password')) {

                        return redirect('/teacher/change-password');

                    }

                }

            }

        }

        return $next($request);

    }

}
