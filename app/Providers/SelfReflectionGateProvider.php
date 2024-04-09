<?php

namespace App\Providers;

use App\Domains\SelfReflection\Actions\ReportCycles;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class SelfReflectionGateProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('parent-can-view-pupil', function ($user, int $pupilId) {
            if (!$user->isParent()) {
                return Response::deny('You are not a parent user');
            }
            try {
                $result = Http::pastoralModule()->post("selfreflections/pupils/" . $pupilId . "/contacts")
                    ->throw()
                    ->object();
            } catch (RequestException $exception) {
                return Response::deny($exception->response->json()['message']);
            }
            if (!in_array($user->email, $result)) {
                return Response::deny('You are not a contact for this pupil');
            }
            return Response::allow();
        });

        Gate::define('report-editable', function ($user, int|object $reportCycle) {

            if (is_int($reportCycle)) {
                $reportCycle = ReportCycles::find($reportCycle);
            }

            if (now() > $reportCycle->EndDate) {
                return false;
            }
            return true;
        });
    }
}
