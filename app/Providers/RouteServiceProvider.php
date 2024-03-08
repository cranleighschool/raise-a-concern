<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/submit';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            $this->domainRoutes([
                'raiseaconcern',
                'selfreflection'
            ]);


        });
    }

    private function domainRoutes(array $routes)
    {
        Route::middleware('csp')->group(function() use ($routes) {
            foreach ($routes as $route) {
                Route::group([
                    'domain' => config('app.domains.' . $route . '.url'),
                    'as' => $route . '.'
                ], function () use ($route) {
                    Route::middleware('web')
                        ->group(base_path('routes/' . $route . '.php'));
                });
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
