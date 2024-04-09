@extends('layouts.raiseaconcern')

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
    @if (auth()->guest())
    <div class="alert alert-warning">
        <p class=""><strong>You are not <a href="{{ route('raiseaconcern.login') }}">logged in</a>.</strong> In most cases it's best if we know who is raising the
            concern, in case there's further details we
            need to in order keep someone safe. However, if you really want to remain anonymous, please continue the
            form below. <a href="{{ route('raiseaconcern.login') }}">Log In</a>.</p>
    </div>
    @endif
    <p class="lead">Please tell us about your concern...</p>
    <form id="create-concern" method="post" action="{{ route('raiseaconcern.store') }}" class="form">
        {{ csrf_field() }}
        {{ method_field('POST') }}
        <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
            <label class="form-label" for="subject-input">Subject</label>
            <div class="small"><em>Imagine you were writing a subject for the email...</em></div>
            <input type="text"
                   id="subject-input"
                   class="form-control form-control-lg"
                   name="subject"
                   placeholder="Subject"
                   required="required"
                   value="{{ old('subject') }}"
            >
            @include('partials.input-error',['inputName' => 'subject'])
        </div>
<br />
        <div class="form-group{{ $errors->has('person_type') ? ' has-error' : '' }}">
            @if (old('person_type') == 'pupil')
                @php ($personTypePupil = true)
                @php ($personTypeStaff = false)
                @php ($personTypeHead = false)
            @elseif (old('person_type') == 'staff')
                @php ($personTypePupil = false)
                @php ($personTypeStaff = true)
                @php ($personTypeHead = false)
            @elseif (old('person_type') == 'headmaster')
                @php ($personTypePupil = false)
                @php ($personTypeStaff = false)
                @php ($personTypeHead = true)
            @else
                @php ($personTypePupil = false)
                @php ($personTypeStaff = false)
                @php ($personTypeHead = false)
            @endif
            <div class="input-group input-group-lg">
                <span class="input-group-text" id="basic-addon1">Regarding a:</span>

                <input id="person_type_pupils" class="btn-check" type="radio" name="person_type"
                {{ $personTypePupil ? 'checked="checked' : ''}} value="pupil"/>
                <label class="btn btn-cranleigh" for="person_type_pupils">Pupil</label>

                <input class="btn-check" id="person_type_staff" type="radio" name="person_type"
                       {{ $personTypeStaff ? 'checked="checked"' : '' }} value="staff"/>
                <label class="btn btn-cranleigh" for="person_type_staff">Staff Member</label>

                <input class="btn-check" id="person_type_head" type="radio" name="person_type"
                       {{ $personTypeHead ? 'checked="checked"' : '' }} value="headmaster"/>
                <label class="btn btn-cranleigh" for="person_type_head">Headmaster</label>
            </div>
            <br/>@include('partials.input-error',['inputName' => 'person_type'])
            <div id="notified-container" class="alert alert-primary visually-hidden">This will create a notification to: <span id="whogetsnotified"></span></div>
        </div>

        <div class="form-group{{ $errors->has('school_id') ? ' has-error' : '' }}">
            @if (old('school_id') == 1)
                @php ($senior = true)
                @php ($prep = false)
                @php ($unknown = false)
            @elseif (old('school_id') == 2)
                @php ($senior = false)
                @php ($prep = true)
                @php ($unknown = false)
            @else
                @php ($senior = false)
                @php ($prep = false)
                @php ($unknown = false)
            @endif
            <div class="input-group-lg input-group">
                <span class="input-group-text" id="basic-addon1">School:</span>

                <input class="btn-check" type="radio" id="school_cs" name="school_id"
                       {{ $senior ? 'checked="checked' : ''}} value="1"/>
                <label class="btn btn-cranleigh" for="school_cs">Cranleigh Senior School</label>

                <input class="btn-check" id="school_prep" type="radio" name="school_id"
                       {{ $prep ? 'checked="checked"' : '' }} value="2"/>
                <label class="btn btn-cranleigh" for="school_prep">Cranleigh Prep School</label>

                <input class="btn-check" type="radio" id="school_unknown" name="school_id"
                       {{ $unknown ? 'checked="checked"' : '' }} value=""/>
                <label class="btn btn-cranleigh" for="school_unknown">Unknown</label>
            </div>
            <br/>@include('partials.input-error',['inputName' => 'school_id'])

        </div>

        <div class="form-group{{$errors->has('concern') ? ' has-error' : '' }}">
            <label for="concern">Please describe your concern</label>
                                <textarea id="concern" name="concern" cols="8" rows="10"
                                          class="form-control wysiwyg">{{ old('concern') }}</textarea>
            @include('partials.input-error',['inputName' => 'concern'])
        </div>
        <br/>
        <button type="submit" class="btn btn-lg btn-dark">
            {{ $submitButtonText ?? 'Submit' }}
        </button>
    </form>
@endsection
