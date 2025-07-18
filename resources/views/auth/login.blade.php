@extends('layouts.raiseaconcern')

@section('content')

    <h2>Welcome</h2>
    <p class="lead pb-4 mb-0">Please click the relevant button below. You will be directed to Firefly which will manage
        the login
        process.</p>

    <div class="row">
        <div class="col-6">
            <div class="card login-card">
                <x-logo class="card-img-top p-5"/>
                <div class="card-body">
                    <h3 class="card-title text-blue">Senior School</h3>
                    <p class="card-text">Parents, Pupils and Staff login with Firefly below.</p>
                    <div class="d-grid">
                        <a aria-label="Login using Senior School Firefly" href="{{ route('raiseaconcern.firefly-login', 'senior') }}"
                           class="btn btn-gold-fill stretched-link">Login with Firefly</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card login-card">
                <x-logo school="CPS" class="card-img-top p-5"/>
                <div class="card-body">
                    <h3 class="card-title text-blue">Prep School</h3>
                    <p class="card-text">Parents, Pupils and Staff login with Firefly below.</p>
                    <div class="d-grid">
                        <a aria-label="Login using Prep School Firefly"
                           href="{{ route('raiseaconcern.firefly-login', 'prep') }}"
                           class="btn btn-gold-fill stretched-link">Login with Firefly</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="lead text-center pt-4">In most cases it's best if we know who is raising the concern, in case there are
        further
        details we
        need to in order keep someone safe.<br/>However, if you really want to remain anonymous <a
            href="{{ route('raiseaconcern.submit') }}">you can do that too.</a></p>

@endsection
