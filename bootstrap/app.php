<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\FrameGuard;
use Illuminate\Support\Facades\Route;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            foreach (['raiseaconcern', 'selfreflection'] as $route) {
                Route::domain(config('app.domains.'.$route.'.url'))
                    ->as($route.'.')
                    ->name($route.'.')
                    ->group(function () use ($route) {
                        Route::middleware('web')
                            ->group(base_path('routes/'.$route.'.php'));
                    });
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/submit');
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\AddNonceHeaders::class,
            SecurityHeaders::class,
            AddCspHeaders::class,
            FrameGuard::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
