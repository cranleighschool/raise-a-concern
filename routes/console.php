<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyMinute();

Artisan::command('announce-alive', function () {
    \Illuminate\Support\Facades\Log::info('The app has reincarnated! (it\'s ready to use again!)');
})->purpose('For alerting us on slack, that the app has been deployed.')->describe('Announce that the app is alive.');
