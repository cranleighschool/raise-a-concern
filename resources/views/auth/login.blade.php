@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <div class="alert alert-info text-center">
                        Please click the relevant button below. You will be directed to Firefly which will manage the login process.
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <a href="{{ route('firefly-login', 'senior')}}" class="btn btn-lg btn-primary">Senior School</a>
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="{{ route('firefly-login', 'prep')}}" class="btn btn-lg btn-primary">Prep School</a>
                        </div>
                    </div>
                </div>
            </div>
            <p>It's best in most cases if we know who is raising the concern, in case there's further details we need to help keep someone safe. However if you really want to remain anonymous <a href="{{ route('submit') }}">you can do that too.</a></p>
        </div>
    </div>
</div>
@endsection
