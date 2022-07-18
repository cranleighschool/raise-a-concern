<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
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


Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
]);

Route::get('/', function () {
    return view('welcome');
});

Route::get('firefly/{school}', [LoginController::class, 'loginRedirect'])->name('firefly-login');
Route::get('firefly/{school}/success', [LoginController::class, 'callbackSuccess'])->name('firefly-success');


Route::get('/home', [HomeController::class, 'index'])->name('home');
