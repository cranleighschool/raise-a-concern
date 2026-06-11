<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\RunHealthChecksCommand;

Schedule::command(RunHealthChecksCommand::class)->everyMinute();

Artisan::command('announce-alive', function () {
    Log::info('The app has reincarnated! (it\'s ready to use again!)');
})->purpose('For alerting us on slack, that the app has been deployed.')->describe('Announce that the app is alive.');
