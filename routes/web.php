<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

// The below are not part of the CSP
Route::get('health', HealthCheckResultsController::class)->name('health');
Route::get('health.json', HealthCheckJsonResultsController::class)->name('health.json');

Route::post('csp-report', function (\Illuminate\Http\Request $request) {
    $payload = json_decode($request->getContent(), true);

    \Illuminate\Support\Facades\Log::info('CSP Violation:', $payload);

    return response()->noContent();

})->name('csp-report');
