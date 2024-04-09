<div>
    <h2>{{ $reflection->subject }}</h2>
    @foreach($reflection->teachers as $teacher)
        <h3>{{ sprintf('%s %s', $teacher->title, $teacher->surname) }}</h3>
        @if ($current->where("teacher_id", $teacher->staff_id)->where("teaching_set_id", $reflection->id)->first())
            <div class="alert alert-secondary">
                <p>
                    <em>{{ $current->where("teacher_id", $teacher->staff_id)->where("teaching_set_id", $reflection->id)->first()['reflection'] }}</em>
                </p>
                <p class="float-end">
                    @can('report-editable', $reportCycleId)
                        <a class="btn btn-sm btn-outline-secondary"
                           href="{{ route('selfreflection.compose', ['reportCycle' => $reportCycleId, 'teachingSet' => $reflection->id, 'teacher' => $teacher->staff_id]) }}">Edit
                            Reflection</a>
                    @endcan
                </p>
                <br/>
            </div>
        @else
            @can('report-editable', $reportCycleId)
            <a href="{{ route('selfreflection.compose', ['reportCycle' => $reportCycleId, 'teachingSet' => $reflection->id, 'teacher' => $teacher->staff_id]) }}"
               class="btn btn-lg btn-outline-primary">Add Reflection</a>
            @else
                <p class="text-danger">A self reflection was not entered.</p>
            @endcan
        @endif
    @endforeach
    <hr/>
</div>
