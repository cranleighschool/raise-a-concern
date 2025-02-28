<?php

use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Domains\SelfReflection\Http\LoginController;
use App\Domains\SelfReflection\Http\LookupController;
use App\Domains\SelfReflection\Http\SelfReflectionPupilController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('selfreflection.home', [
        'reportCycles' => ReportCycles::all(),
    ]);
})->name('home');

Route::get('impersonation/{pupilId}', [LoginController::class, 'impersonate'])
    ->name('impersonate');


Route::get('login', [LoginController::class, 'redirectLogin'])
    ->name('login');
Route::get('login/callback/success', [LoginController::class, 'callbackSuccess'])
    ->name('login.callback.success');
Route::get('login/callback/failure', [LoginController::class, 'callbackFailure'])
    ->name('login.callback.failure');


Route::middleware('auth')->group(function () {
    Route::get('pupil/{pupilId}', [SelfReflectionPupilController::class, 'index'])->name('pupil.index');

    Route::post('cycle', [SelfReflectionPupilController::class, 'chooseCycle'])
        ->name('submit');
    Route::get('{reportCycle}/pupil/{pupilId}', [SelfReflectionPupilController::class, 'show'])->name('showget');

    Route::get('{reportCycle}/compose/{teachingSet}/{teacher}', [SelfReflectionPupilController::class, 'edit'])
        ->name('compose');
    Route::post('{reportCycle}/save/{teachingSet}/{teacher}', [SelfReflectionPupilController::class, 'store'])
        ->name('save');

    Route::get('lookup/{reportCycle}', LookupController::class)
        ->name('lookup');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
