<?php

namespace App\Domains\SelfReflection\Http;

use App\Domains\SelfReflection\Actions\PupilData;
use Illuminate\Http\RedirectResponse;

class LookupController
{
    public function __invoke(int $reportCycle): RedirectResponse
    {
        if (auth()->user()->isStaff()) {
            abort(403, 'This page is intended for pupils and parents only. Staff should use the self-reflection page within their Teaching Set in the pastoral module.');
        }

        $pupilData = new PupilData();


        return redirect()->route('selfreflection.showget', ['reportCycle' => $reportCycle, 'pupilId' => $pupilData->pupil_id]);

    }
}
