<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
]);

Route::get('health', HealthCheckResultsController::class)->name('health');
Route::get('health.json', HealthCheckJsonResultsController::class)->name('health.json');
