<?php

use App\Domains\SelfReflection\Actions\ReportCycles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

// TODO - protect this route with middleware that only allows access from the internal network

Route::get('report-cycles', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'withoutFilter' => 'boolean|nullable',
    ], [
        'withoutFilter.boolean' => 'The withoutFilter field must be a boolean (for a url that means either `1` or `0`)',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'There was an error with your request', 'validation_errors' => $validator->errors()], 400);
    }

    $withoutFilter = (bool) $request->get('withoutFilter', true);

    if ($request->has('refresh')) {
        Cache::forget('get-all-report-cycles'.$withoutFilter);
    }

    $data = Cache::remember('get-all-report-cycles'.$withoutFilter, now()->addDay(), function () use ($withoutFilter) {
        return ReportCycles::all(withoutFilter: $withoutFilter)
            ->map(function (object $obj) {
                return [
                    'reportCycleId' => (int)$obj->reportCycleId,
                    'CycleName' => $obj->CycleName,
                    'StartDate' => $obj->StartDate,
                    'EndDate' => $obj->EndDate,
                    'ReportYear' => (int) $obj->ReportYear,
                ];
            })->sortByDesc('EndDate')->values();
    });

    return response()->json($data);
});
