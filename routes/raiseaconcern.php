<?php

use App\Domains\RaiseAConcern\Http\ConcernController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

\Illuminate\Support\Facades\Auth::login(\App\Models\User::first());

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('login/firefly/{school}', [LoginController::class, 'loginRedirect'])
    ->name('firefly-login');

Route::get('login/firefly/{school}/success', [LoginController::class, 'callbackSuccess'])
    ->name('firefly-success');

Route::get('submit', [ConcernController::class, 'index'])->name('submit');
Route::post('submit', [ConcernController::class, 'store'])->name('store');
