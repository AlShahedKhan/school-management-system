<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Sanctum Stateful Middleware (For API Auth)
        |--------------------------------------------------------------------------
        */
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Custom Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'public.locale' => \App\Http\Middleware\SetPublicLocale::class,
            'check.default.password' => \App\Http\Middleware\CheckTeacherDefaultPassword::class,
        ]);

        // Enable Sanctum authentication for web routes
        $middleware->statefulApi();
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", ['exception' => $e], $status);
            }
        });
    })

    ->create();
