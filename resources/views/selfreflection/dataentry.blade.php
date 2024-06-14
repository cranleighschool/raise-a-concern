<x-selfreflection pageTitle="Home">
    <div class="alert alert-info">
        <p class="lead">Instructions for pupils:</p>
        <p>Following your summer assessments, we would like you to reflect on your progress in each of the subjects you
            will carry on next year for your GCSEs. Your teachers will see these comments before writing their end of
            term reports and they will form an important part of the discussion with your tutor. Your comments will also
            be sent home to your parents/guardians.
        </p>
        <p>
            The things you should be focussing on in your comments are:</p>
        <ol>
            <li>
                Your effort and engagement in class and for prep throughout the year
            </li>
            <li>What went well and what would you do differently next year when it comes to revising for your
                assessments
            </li>
            <li>Specific things you feel you need to work on to improve at the start of next year</li>
        </ol>
        <p>This is not an opportunity for you to comment on your teacher, or indeed the subjects more generally. This is
            all about you, your strengths and weaknesses and what you intend to do in order to reach your potential in
            your GCSEs. You should be aiming to write 4/5 lines for each subject and your teacher may give you
            additional prompts/suggestions.
        </p>

    </div>
    <h1>Your reflections
        for {{ \App\Domains\SelfReflection\Actions\ReportCycles::find($reportCycleId)->CycleName }}</h1>
    @foreach ($subjects as $reflection)
        <livewire:self-reflection :reflection="$reflection" :current="$current" :reportCycleId="$reportCycleId"/>
    @endforeach
</x-selfreflection>
