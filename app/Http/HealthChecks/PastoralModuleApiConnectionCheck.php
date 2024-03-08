<?php

namespace App\Http\HealthChecks;

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PastoralModuleApiConnectionCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();
        try {
            $user = Http::pastoralModule()->get('auth/me')->throw()->collect()->first();
        } catch (\Exception $e) {
            return $result->failed('Error thrown whilst trying to access Pastoral Module API User');
        }
        if (is_null($user)) {
            return $result->failed('Pastoral Module API User is inaccessible');
        }

        if ($user['username'] === 'RAISEACONCERNAPP' && $user['enabled'] === true) {
            return $result->ok('Pastoral Module API User is accessible');
        }
        return $result->warning('Pastoral Module API User is not accessible. Unknown issue.');
    }

}
