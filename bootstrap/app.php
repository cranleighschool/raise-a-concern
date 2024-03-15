<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            $routes = ['raiseaconcern', 'selfreflection'];
            Route::middleware(AddCspHeaders::class)
                ->group(function () use ($routes) {
                    foreach ($routes as $route) {
                        Route::domain(config('app.domains.' . $route . '.url'))
                            ->as($route . '.')
                            ->name($route . '.')
                            ->group(function () use ($route) {
                                Route::middleware(['web', AddCspHeaders::class])
                                    ->group(base_path('routes/' . $route . '.php'));
                            });
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\RedirectIfAuthenticated::class,
            SecurityHeaders::class,
            \Illuminate\Http\Middleware\FrameGuard::class
        ]);
        $middleware->alias(['csp' => AddCspHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
