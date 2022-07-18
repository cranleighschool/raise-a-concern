<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('redirect', [\App\Http\Controllers\Auth\LoginController::class, 'googleRedirect']);
Route::get('callback', [\App\Http\Controllers\Auth\LoginController::class, 'googleRedirect']);

/*Route::any('callback', function(\Illuminate\Http\Request $request) {
    if ($request->has('ffauth_secret')) {
        \Illuminate\Support\Facades\Http::get('https://cranleigh.fireflycloud.net/login/api/sso?ffauth_device_id=raiseaconcern-cranleigh');
    }
    \Illuminate\Support\Facades\Log::debug($request->all());
});
*/

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
