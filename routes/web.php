<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::middleware('csp')->group(function() {
    Auth::routes([
        'register' => false,
        'reset' => false,
        'confirm' => false,
    ]);

});


// The below are not part of the CSP
Route::get('health', HealthCheckResultsController::class)->name('health');
Route::get('health.json', HealthCheckJsonResultsController::class)->name('health.json');
