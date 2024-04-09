<?php

namespace App\Domains\SelfReflection\Http;

use App\Domains\SelfReflection\Actions\PupilData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LookupController
{

    public function __invoke(int $reportCycle): RedirectResponse
    {
        $pupilData = new PupilData();

        return redirect()->route('selfreflection.showget', ['reportCycle' => $reportCycle, 'pupilId' => $pupilData->pupil_id]);

    }
}
