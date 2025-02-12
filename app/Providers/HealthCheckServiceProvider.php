<?php

namespace App\Providers;

use App\Http\HealthChecks\IsamsBatchApiHealthCheck;
use App\Http\HealthChecks\PastoralModuleApiConnectionCheck;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthCheckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Health::checks([
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            PingCheck::new()->name('Pastoral Ping')->url('https://pastoral.cranleigh.org'),
            PastoralModuleApiConnectionCheck::new()->name('Api User'),
            IsamsBatchApiHealthCheck::new()->name('Isams Batch Api'),
            //PingCheck::new()->name('Senior Firefly Ping')->url(url('login/firefly/senior')),
            //PingCheck::new()->name('Prep Firefly Ping')->url(url('login/firefly/prep')),
        ]);

    }
}
