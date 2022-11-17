@extends('layouts.app')

@section('content')

    <h2>Welcome</h2>
    <p class="lead">Please click the relevant button below. You will be directed to Firefly which will manage the login process.</p>
    <div class="row login-images">
        <div class="col-md-6 text-center" style="padding:40px;">
            <a href="{{ route('firefly-login', 'senior') }}">
                <img class="img-thumbnail" style="padding:20px;" src="{{ asset('storage/CranleighLogo.png') }}"/>
            </a>
        </div>
        <div class="col-md-6 text-center" style="padding:40px;">
            <a href="{{ route('firefly-login', 'prep')}}">
                <img class="img-thumbnail" style="padding:20px;" src="{{ asset('storage/CranleighPrepLogo.png') }}"/>
            </a>
        </div>
    </div>

    <p class="lead text-center">In most cases it's best if we know who is raising the concern, in case there are further details we
        need to in order keep someone safe. However, if you really want to remain anonymous <a
            href="{{ route('submit') }}">you can do that too.</a></p>

@endsection
