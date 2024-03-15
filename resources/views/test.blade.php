@extends('layouts.raiseaconcern')

@section('content')

    <h2>Welcome</h2>
    <p class="lead">Please click the relevant button below. You will be directed to Firefly which will manage the login
        process.</p>
    <h1>Guest Test</h1>
    @guest
        YOU ARE A GUEST
    @else
        YOU ARE LOGGED IN
    @endguest
    <h1>Auth Test</h1>
    @if (auth()->user())
        YOU ARE LOGGED IN
    @else
        YOU ARE A GUEST
    @endif

    <form method="POST" action="{{ route('testpost') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    {{ dump(\Illuminate\Support\Facades\Cache::get('sessionDebugData')) }}
    {{ dump(csrf_token()) }}





@endsection
