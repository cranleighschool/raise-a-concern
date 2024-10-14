<x-selfreflection pageTitle="Home">
    @guest
        <p class="lead">You are not logged in. Please <a href="{{ route('selfreflection.login') }}">login</a> to continue.</p>
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

            <div class="mb-3">
                <p>As parents' meetings approach, we would like you to reflect on your progress in each of your subjects. Your teachers will see these comments before meeting your paretns after half term and they will form an important part of the discussion with your tutor.</p>
                <p>
                    The things you should be focussing on in your comments are:</p>
                    <ol>
                    <li>Your effort and engagement in class and for prep so far this term</li>
                    <li>What has gone well and what would you do differently after half term</li>
                    <li>Specific things you feel you need to work on to improve</li>
                </ol>
                <p>This is not an opportunity for you to comment on your teacher, or indeed the subjects more generally. This is all about you, your strengths and weaknesses and what you intend to do in order to reach your potential in your GCSEs or A-Levels/BTECs. You should be aiming to write 4/5 lines for each subject and your teacher may give you additional prompts/suggestions.
                </p>
            </div>
        <div class="mb-3">
            <p class="lead">First, select the reporting cycle you have been asked to reflect on:</p>
        </div>

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
        @if (auth()->user()->isStaff())
            You are a staff member - you should probably be using the <a href="https://pastoral.cranleigh.org">Pastoral Module</a>'s Tutor Dashboard to read self reflections.
        @endif
    @endguest
</x-selfreflection>
