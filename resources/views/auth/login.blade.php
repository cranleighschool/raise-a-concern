@extends('layouts.raiseaconcern')

@section('content')



    <img src="/logo.png" style="height: 200px;margin:auto;display:block;margin-top:10px;margin-bottom:40px;"alt="Cranleigh School logo"/>

{{--    <p class="lead pb-2 mb-0 text-center">Please click the relevant button below.</p>--}}
    <p class="lead pb-4 mb-0 text-center">By clicking the relevant button below, you will be directed to Firefly which will manage
        the login
        process.</p>


    <div class="row">
        <div class="col-6">
            <div class="card login-card">
                <img aria-hidden="true" src="/senior.jpg" class="card-img-top" alt="Cranleigh School Logo">
                <div class="card-body">
                    <h3 class="card-title text-blue">Cranleigh Senior</h3>
                    <p class="card-text">Parents, Pupils and Staff login with Firefly below.</p>
                    <div class="d-grid">
                        <a aria-label="Login using Senior School Firefly"
                           href="{{ route('raiseaconcern.firefly-login', 'senior') }}"
                           class="btn btn-gold-fill stretched-link">Login with Firefly</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card login-card">
                <img aria-hidden="true" src="/prep.jpg" class="card-img-top" alt="Cranleigh School Logo">
                <div class="card-body">
                    <h3 class="card-title text-blue">Cranleigh Prep</h3>
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
    <p class="lead text-center pt-4 d-block d-xl-none">In most cases it's best if we know who is raising the concern, in case there are
        further details we need to in order keep someone safe.</p><p class="lead text-center pt-2 d-block d-xl-none">However, if you really want to remain anonymous <a
            href="{{ route('raiseaconcern.submit') }}">you can do that too.</a></p>

@endsection
