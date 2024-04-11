<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

// The below are not part of the CSP
Route::get('health', HealthCheckResultsController::class)->name('health');
Route::get('health.json', HealthCheckJsonResultsController::class)->name('health.json');
