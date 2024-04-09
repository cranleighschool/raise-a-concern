<x-selfreflection pageTitle="Home">
    <h1>Your reflections
        for {{ \App\Domains\SelfReflection\Actions\ReportCycles::find($reportCycleId)->CycleName }}</h1>
    @foreach ($subjects as $reflection)
        <livewire:self-reflection :reflection="$reflection" :current="$current" :reportCycleId="$reportCycleId"/>
    @endforeach
</x-selfreflection>
