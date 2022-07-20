@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
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
                        <form id="create-concern" method="post" action="{{ route('store') }}" class="form">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                                <label>Subject</label>
                                <input type="text"
                                       class="form-control"
                                       name="subject"
                                       value="{{ old('subject') }}"
                                >
                                @include('partials.input-error',['inputName' => 'subject'])
                            </div>

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
                                <label>Regarding a</label>
                                <br/>
                                <input type="radio" name="person_type"
                                       {{ $personTypePupil ? 'checked="checked' : ''}} value="pupil"/> Pupil
                                <br/>
                                <input type="radio" name="person_type"
                                       {{ $personTypeStaff ? 'checked="checked"' : '' }} value="staff"/> Staff Member
                                <br/>
                                <input type="radio" name="person_type"
                                       {{ $personTypeHead ? 'checked="checked"' : '' }} value="headmaster"/> Headmaster
                                <br/>@include('partials.input-error',['inputName' => 'person_type'])
                            </div>

                            <div class="form-group{{ $errors->has('school_id') ? ' has-error' : '' }}">
                                @if (old('school') == 1)
                                    @php ($senior = true)
                                    @php ($prep = false)
                                    @php ($unknown = false)
                                @elseif (old('school') == 2)
                                    @php ($senior = false)
                                    @php ($prep = true)
                                    @php ($unknown = false)
                                @elseif (old('school') == null)
                                    @php ($senior = false)
                                    @php ($prep = false)
                                    @php ($unknown = true)
                                @else
                                    @php ($senior = false)
                                    @php ($prep = false)
                                    @php ($unknown = false)
                                @endif
                                <label>School</label>
                                <br/>
                                <input type="radio" name="school_id" {{ $senior ? 'checked="checked' : ''}} value="1"/>
                                Cranleigh Senior School
                                <br/>
                                <input type="radio" name="school_id" {{ $prep ? 'checked="checked"' : '' }} value="2"/>
                                Cranleigh Prep School
                                <br/>
                                <input type="radio" name="school_id"
                                       {{ $unknown ? 'checked="checked"' : '' }} value=""/> Unknown
                                @include('partials.input-error',['inputName' => 'school_id'])

                            </div>

                            <div class="form-group{{$errors->has('concern') ? ' has-error' : '' }}">
                                <textarea id="concern" name="concern" cols="8" rows="10"
                                          class="form-control wysiwyg">{{ old('concern') }}</textarea>
                                @include('partials.input-error',['inputName' => 'concern'])
                            </div>

                            <button type="submit" class="btn btn-lg btn-dark">
                                {{ $submitButtonText ?? 'Submit' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
