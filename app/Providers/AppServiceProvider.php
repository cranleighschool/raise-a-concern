<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Everything strict, all the time.
        // (https://planetscale.com/blog/laravels-safety-mechanisms)
        Model::shouldBeStrict();

        // In production, merely log lazy loading violations.
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);

                info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        Http::macro('pastoralModule', function() {
            return Http::withUserAgent("RaiseAConcern")
                       ->withToken(config('pastoral-module.apiToken'))
                       ->baseUrl(config('pastoral-module.apiUrl'))
                       ->acceptJson();
        });
    }
}
