<?php

use App\Domains\RaiseAConcern\Http\ConcernController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::loginUsingId(1);

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
]);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login/firefly/{school}', [LoginController::class, 'loginRedirect'])
     ->name('firefly-login');
Route::get('login/firefly/{school}/success', [LoginController::class, 'callbackSuccess'])
     ->name('firefly-success');


Route::get('submit', [ConcernController::class, 'index'])->name('submit');
Route::post('submit', [ConcernController::class, 'store'])->name('store');

Route::get('health', HealthCheckResultsController::class);
Route::get('health.json', HealthCheckJsonResultsController::class);
