<?php

namespace App\Http\HealthChecks;

use App\Domains\SelfReflection\Actions\ReportCycles;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class IsamsBatchApiHealthCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();
        try {
            $reportCycles = ReportCycles::all();
            if ($reportCycles->isEmpty()) {
                $allReportCycles = ReportCycles::all(true);
                if ($allReportCycles->isEmpty()) {
                    return $result->failed('All Report Cycles are missing');
                }
                return $result->warning('There are not active Report Cycles');
            }

            return $result->ok();
        } catch (Exception $e) {
            Log::error('Health Check: '.$e->getMessage(), [$e]);

            return $result->failed($e->getMessage());
        }
    }
}
