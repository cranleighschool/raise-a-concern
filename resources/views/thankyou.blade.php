@extends('layouts.app')

@section('content')
    <h2>Raising a Concern</h2>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-success">
        <p class="lead">Thank you for submitting your concern.</p>
        <p>This will now be reviewed by {{ $reviewer }}.</p>
        <p>Your reference number is <strong>#{{ $concernId }}</strong>.</p>
        @if (optional(auth()->user())->sso_type === 'parents')
            <p>There is no online facility to check the status of your concern at present, but you are welcome to speak
                directly to a member of the safeguarding team using the details on this page, if you do not receive suitable
                response. (Please quote the reference number)</p>
        @elseif(auth()->guest())
            <p>Because you were not logged in there is no automated way for us to contact you again. If you happened to
                leave contact details within your concern then {{ $reviewer }} will use that if required.</p>
            <p>Otherwise, if
                you want to follow up on your concern please contact us using the details on this page, leaving a name and contact details and your reference number above.</p>
        @else
            <p>You are welcome to log into the <a href="https://pastoral.cranleigh.org">Pastoral Module</a>, when on the School campus, to check the status of your
                concern. Alternatively you can speak directly to a member of the safeguarding team using the details on
                this page.</p>
        @endif
    </div>
    <h3>Where now?</h3>
    <a href="{{ route('home') }}" class="btn btn-cranleigh">Home</a>
    <a href="https://www.cranleigh.org" class="btn btn-cranleigh">cranleigh.org</a>
    <a href="https://www.cranprep.org" class="btn btn-cranprep">cranprep.org</a>
@endsection
