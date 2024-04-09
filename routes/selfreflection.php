<?php

use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Domains\SelfReflection\Http\SelfReflectionPupilController;
use Illuminate\Support\Facades\Route;

Route::get('test', function() {
    \Illuminate\Support\Facades\Gate::authorize('parent-can-view-pupil', 198);

    return 'Hello World';
});

Route::get('/', function () {
    return view('selfreflection.home', [
        'reportCycles' => ReportCycles::all(),
    ]);
})->name('home');

Route::get('pupil/{pupilId}', [SelfReflectionPupilController::class, 'index'])->name('pupil.index');

Route::post('cycle', [SelfReflectionPupilController::class, 'chooseCycle'])
    ->name('submit');
Route::get('{reportCycle}/pupil/{pupilId}', [SelfReflectionPupilController::class, 'show'])->name('showget');

Route::get('{reportCycle}/compose/{teachingSet}/{teacher}', [SelfReflectionPupilController::class, 'edit'])
    ->name('compose');
Route::post('{reportCycle}/save/{teachingSet}/{teacher}', [SelfReflectionPupilController::class, 'store'])
    ->name('save');

Route::get('lookup/{reportCycle}', \App\Domains\SelfReflection\Http\LookupController::class)
    ->name('lookup');
