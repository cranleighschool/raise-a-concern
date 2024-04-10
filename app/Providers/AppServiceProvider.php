<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        Http::macro('pastoralModule', function () {
            return Http::withUserAgent('RaiseAConcern')
                ->withToken(config('pastoral-module.apiToken'))
                ->baseUrl(config('pastoral-module.apiUrl'))
                ->acceptJson();
        });
    }
}
