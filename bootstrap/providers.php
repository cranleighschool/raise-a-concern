<?php

use App\Providers\AppServiceProvider;
use App\Providers\HealthCheckServiceProvider;
use App\Providers\SelfReflectionGateProvider;

return [
    AppServiceProvider::class,
    HealthCheckServiceProvider::class,
    SelfReflectionGateProvider::class,
];
