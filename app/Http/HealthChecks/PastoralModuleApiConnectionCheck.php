<?php

namespace App\Http\HealthChecks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PastoralModuleApiConnectionCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();
        try {
            $user = Http::pastoralModule()->connectTimeout(300)->get('auth/me')->throw()->collect()->first();
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $result->failed('Error thrown whilst trying to access Pastoral Module API User');
        }
        if (is_null($user)) {
            return $result->failed('Pastoral Module API User is inaccessible');
        }

        if ($user['username'] === 'RAISEACONCERNAPP' && $user['enabled'] === true) {
            return $result->ok();
        }

        return $result->warning('Pastoral Module API was unable to get the right result.');
    }
}
