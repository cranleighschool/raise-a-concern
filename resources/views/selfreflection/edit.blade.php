<x-selfreflection pageTitle="Home">
    <h1>Your reflections of {{ $subject }} with {{ $teacher }} for {{ $reportCycle->CycleName }}</h1>
    <form action="{{ route('selfreflection.save',[$reportCycle->reportCycleId,$teachingSetId, $teacherId]) }}"
          method="POST">
        @csrf
        <div class="form-floating mb-3">
        <textarea class="form-control" name="reflection" id="reflection"
                  rows="10" style="height: 200px;">{{ old('reflection', $current['reflection'] ?? '') }}</textarea>
            <label for="reflection">Reflection</label>
        </div>
        <button class="btn-lg btn btn-outline-primary" type="submit">Save</button>
        <a href="{{ url()->previous() }}" class="btn-lg btn btn-outline-secondary">Back</a>
        <input type="hidden" name="reportCycleId" value="{{ $reportCycle->reportCycleId }}"/>
        <input type="hidden" name="teacherId" value="{{ $teacherId }}"/>
        <input type="hidden" name="teachingSetId" value="{{ $teachingSetId }}"/>
    </form>
</x-selfreflection>
