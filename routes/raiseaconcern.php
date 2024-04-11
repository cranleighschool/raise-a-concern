<?php

use App\Domains\RaiseAConcern\Http\ConcernController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('raiseaconcern.login');
})->name('home');

Route::get('login/firefly/{school}', [LoginController::class, 'loginRedirect'])
    ->name('firefly-login');

Route::get('login/firefly/{school}/success', [LoginController::class, 'callbackSuccess'])
    ->name('firefly-success');

Route::get('submit', [ConcernController::class, 'index'])->name('submit');
Route::post('submit', [ConcernController::class, 'store'])->name('store');
