@extends('layouts.raiseaconcern')

@section('content')

    <h2>Welcome</h2>
    <p class="lead">Please click the relevant button below. You will be directed to Firefly which will manage the login
        process.</p>

    <div class="row login-columns">
        <div class="col-6">
            <div class="card">
                <a href="{{ route('raiseaconcern.firefly-login', 'senior') }}">
                    <img class="card-img-top" style="padding:20px;" src="{{ asset('storage/CranleighLogo.png') }}"/>
                </a>
                <div class="card-body">
                    <h5 class="card-title">Senior School</h5>
                    <p class="card-text">Parents, Pupils and Staff login with Firefly below.</p>
                    <div class="d-grid">
                        <a href="{{ route('raiseaconcern.firefly-login', 'senior') }}" class="btn btn-cranleigh btn-block">Login with
                            Firefly</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <a href="{{ route('raiseaconcern.firefly-login', 'prep')}}">
                    <img class="card-img-top" style="padding:20px;" src="{{ asset('storage/CranleighPrepLogo.png') }}"/>
                </a>
                <div class="card-body">
                    <h5 class="card-title">Prep School</h5>
                    <p class="card-text">Parents, Pupils and Staff login with Firefly below.</p>
                    <div class="d-grid">
                        <a href="{{ route('raiseaconcern.firefly-login', 'prep') }}" class="btn btn-cranprep">Login with Firefly</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="lead text-center">In most cases it's best if we know who is raising the concern, in case there are further
        details we
        need to in order keep someone safe. However, if you really want to remain anonymous <a
            href="{{ route('raiseaconcern.submit') }}">you can do that too.</a></p>

@endsection
