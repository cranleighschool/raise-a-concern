<x-selfreflection pageTitle="Home">
    <h1>{{ (new \App\Domains\SelfReflection\Actions\PupilData($pupilId))->prename }}'s reflections</h1>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Report Cycle</th>
            <th>Report Year</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        @forelse($reportCycles->sortByDesc('EndDate') as $reportCycle)
            <tr>
                <td>{{ $reportCycle->CycleName }}</td>
                <td>{{ $reportCycle->ReportYear }}</td>
                <td>{{ \Carbon\Carbon::parse($reportCycle->EndDate)->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('selfreflection.showget', ['pupilId' => $pupilId, 'reportCycle' => $reportCycle->reportCycleId]) }}" class="btn btn-outline-primary">View Reflections</a>
                </td>
            </tr>
        @empty
            <tr class="table-warning">
                <td colspan="4" class="text-center">{{ (new \App\Domains\SelfReflection\Actions\PupilData($pupilId))->prename }} has not written any reflections yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</x-selfreflection>
