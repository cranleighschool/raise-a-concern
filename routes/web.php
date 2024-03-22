<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    \Illuminate\Support\Facades\Auth::routes([
        'register' => false,
        'reset' => false,
        'confirm' => false,
    ]);
});//->middleware(\Spatie\Csp\AddCspHeaders::class);

// The below are not part of the CSP
Route::get('health', \Spatie\Health\Http\Controllers\HealthCheckResultsController::class)->name('health');
Route::get('health.json', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class)->name('health.json');
