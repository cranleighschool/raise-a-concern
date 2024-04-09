<x-selfreflection pageTitle="Home">
    @guest
        <p class="lead">You are not logged in. Please <a href="{{ route('login') }}">login</a> to continue.</p>
    @else
        <p class="lead">You are logged in as {{ auth()->user()->name }}</p>
        @if (auth()->user()->isParent())
            <p class="lead">You are logged in as a parent. You can't submit self reflections. However, to read the
                reflections of your son/daughter click on their name below:</p>
            <ul>
                @foreach (auth()->user()->getPupilsOfParent()->sortByDesc('ncYear') as $pupil)
                    <li>
                        <a href="{{ route('selfreflection.pupil.index', ['pupilId' => $pupil['pupil_id']]) }}">{{ $pupil['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
        @if (auth()->user()->isPupil())
            <form class="form" method="post" action="{{ route('selfreflection.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="reportCycleInput" class="form-label">Select a Reporting Cycle</label>
                    <select class="form-control-lg" name="reportCycle" id="reportCycleInput">
                        <option value="">Select a Reporting Cycle</option>
                        @foreach ($reportCycles->sortByDesc('LastUpdated') as $reportCycle)
                            <option value="{{ $reportCycle->reportCycleId }}">{{ $reportCycle->ReportName }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-lg">Go</button>
                </div>
            </form>
        @endif
    @endguest
</x-selfreflection>
