<?php

use Illuminate\Support\Facades\Route;

//\Illuminate\Support\Facades\Auth::loginUsingId(3);

// The below are not part of the CSP
Route::get('health', \Spatie\Health\Http\Controllers\HealthCheckResultsController::class)->name('health');
Route::get('health.json', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class)->name('health.json');
Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
