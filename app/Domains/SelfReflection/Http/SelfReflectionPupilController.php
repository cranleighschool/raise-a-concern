<?php

namespace App\Domains\SelfReflection\Http;

use App\Domains\SelfReflection\Actions\PupilData;
use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Exceptions\ReportCycleNotFound;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class SelfReflectionPupilController extends Controller
{
    private function authorizeEdit(int $teachingSetId, int $teacherId): bool
    {
        $teachingSets = (new PupilData())->teachingSets;
        if ($teachingSets->pluck('id')->doesntContain($teachingSetId)) {
            return false;
        }
        $teacherIds = $teachingSets->where('id', $teachingSetId)->first()->teachers->pluck('staff_id');

        if ($teacherIds->doesntContain($teacherId)) {
            return false;
        }

        return true;
    }

    public function index(int $pupilId)
    {
        Gate::authorize('parent-can-view-pupil', $pupilId);
        $data = Http::pastoralModule()
            ->get("selfreflections/pupils/" . $pupilId . "/reflections")
            ->throw()
            ->collect();


        $reportCycles = $data->keys()->map(function (int $key) {
            try {
                return ReportCycles::find($key);
            } catch (ReportCycleNotFound $exception) {
                session()->flash('alert-danger', 'Data was found for a report cycle that no longer exists (id: ' . $key . '). Please contact support.');
                return null;
            }
        })->filter();

        return view('selfreflection.index', [
            'reportCycles' => $reportCycles,
            'pupilId' => $pupilId,
        ]);
    }

    /**
     * @throws RequestException
     */
    public function store(int $reportCycleId, int $teachingSetId, int $teacherId, Request $request): RedirectResponse
    {
        if (!$this->authorizeEdit($teachingSetId, $teacherId)) {
            abort(403, 'The combination of pupil, teaching set and teacher is not valid. Malicious activity detected.');
        }

        $data = $request->validate([
            'reflection' => 'required|string|max:5000|min:10',
        ], [
            'reflection.required' => 'Please enter a reflection.',
            'reflection.max' => 'You\'ve written a bit more than we expected. Please keep it to less than 5000 characters.',
            'reflection.min' => 'To be meaningful, please write a longer reflection.',
        ]);

        $writtenReflection = $request->get('reflection');

        $reportCycle = ReportCycles::find($reportCycleId);

        Http::pastoralModule()
            ->post("selfreflections/reports/" . $reportCycleId . "/pupil/" . (new PupilData())->pupil_id, [
                'reflection' => $writtenReflection,
                'teaching_set_id' => $teachingSetId,
                'teacher_id' => $teacherId,
                'academic_year' => $reportCycle->ReportYear,
            ])
            ->throw();

        session()->flash("alert-success", "Reflection saved successfully.");
        return redirect()->route('selfreflection.showget', [
            'reportCycle' => $reportCycleId,
            'pupilId' => (new PupilData())->pupil_id,
        ]);
    }

    public function edit(int $reportCycleId, int $teachingSetId, int $teacherId): View|RedirectResponse
    {
        $reportCycle = ReportCycles::find($reportCycleId);

        if (Gate::check('report-editable', $reportCycle) === false) {
            session()->flash('alert-danger', 'This report cycle has ended and is no longer editable.');

            return redirect()->route('selfreflection.showget', [
                'reportCycle' => $reportCycleId,
                'pupilId' => (new PupilData())->pupil_id,
            ]);
        }

        if (!$this->authorizeEdit($teachingSetId, $teacherId)) {
            abort(403, 'The combination of pupil, teaching set and teacher is not valid. Malicious activity detected.');
        }

        $teachingSets = (new PupilData())->teachingSets;
        $subject = $teachingSets->where('id', $teachingSetId)->first()->subject;
        $teacher = collect($teachingSets->where('id', $teachingSetId)->first()->teachers)->where('staff_id', $teacherId)->first()->name;

        $current = Http::pastoralModule()
            ->get("selfreflections/reports/" . $reportCycleId . "/pupil/" . (new PupilData())->pupil_id)
            ->throw()->collect();

        return view('selfreflection.edit', [
            'subject' => $subject,
            'teacher' => $teacher,
            'reportCycle' => $reportCycle,
            'teachingSetId' => $teachingSetId,
            'teacherId' => $teacherId,
            'current' => collect($current->where('teaching_set_id', $teachingSetId)->where('teacher_id', $teacherId)->first())
        ]);
    }

    /**
     * @throws \Exception
     */
    public function show(int $reportCycle, int $pupilId): View
    {
        Gate::allowIf(function () use ($pupilId) {
            return $pupilId === (new PupilData($pupilId))->pupil_id;
        }, "Don't attempt to change the URL to view another pupil's self-reflection. Malicious activity detected.");

        $current = Http::pastoralModule()
            ->get("selfreflections/reports/" . $reportCycle . "/pupil/" . $pupilId)
            ->throw()->collect();

        return view('selfreflection.dataentry', [
            'subjects' => (new PupilData($pupilId))->teachingSets,
            'current' => $current,
            'reportCycleId' => $reportCycle,
        ]);
    }

    public function chooseCycle(Request $request): RedirectResponse
    {
        $request->validate([
            'reportCycle' => 'required|integer',
        ]);

        return redirect()->route('selfreflection.showget', [
            'reportCycle' => $request->get('reportCycle'),
            'pupilId' => (new PupilData())->pupil_id,
        ]);
    }
}
