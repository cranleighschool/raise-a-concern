<!doctype html>
{{-- Design Idea: https://www.surreycc.gov.uk/children/contact-childrens-services --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Referrer-Policy" content="no-referrer, strict-origin-when-cross-origin">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicons')

    <title>{{ config('app.name', 'Raise a Concern') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" nonce="{{ csp_nonce() }}">

    <!-- Scripts -->
    @vite([
    'resources/js/app.js',
    'resources/sass/app.scss'
    ])

    {{--    @include('partials.tinymce')--}}
</head>
<body>
<div id="app" class="d-flex flex-column min-vh-100">
    <header>
    <h1 class="visually-hidden">{{ config('app.name', 'Raise a Concern') }}</h1>
    <nav class="navbar navbar-dark shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Raise a Concern') }}
            </a>

            <div class="d-flex align-items-center">
                @guest
                    @if (!request()->routeIs('raiseaconcern.login'))
                        <a class="btn btn-gold-fill" href="{{ route('raiseaconcern.login') }}">{{ __('Login') }}</a>
                    @endif
                @endguest

                @auth
                    <form class="ms-2" action="{{ route('raiseaconcern.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-danger" type="submit">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>
    </header>

    <main class="py-4 flex-grow-1">
        <div class="container img-thumbnail bg-white raiseaconcern-container">
            <div class="row">
                <div class="col-12">
                    <p class="lead text-danger text-center">If you think that a child is in immediate danger you should
                        call: <a class="" href="tel:999">999</a>.</p>
                    <hr/>
                </div>
                <div class="col-xl-7 col-12">
                    @if (session()->has('from-pastoral-alert'))
                        <div class="alert alert-warning">
                            <p><strong>Warning</strong></p>
                            <p>{{ session()->pull('from-pastoral-alert') }}</p>
                        </div>
                    @endif
                    {{ displayAlertMsg() }}

                    @yield('content')
                </div>
                <aside id="sidebar" class="col-xl-5 col-12 mt-5 mt-xl-0">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Useful Contacts</h4>
                        </div>
                        <div class="card-body no-padding">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <caption class="visually-hidden">Contact information for safeguarding personnel, including name, phone, and email.</caption>
                                    <thead>
                                    <th scope="col">Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Sue Fairbrother<br/>
                                            <em class="small">Head of Safeguarding</em></td>
                                        <td><a href="tel:+441483927559">01483 927559</a></td>
                                        <td><a href="mailto:sff@cranleigh.org">sff@cranleigh.org</a></td>
                                    </tr>
                                    <tr>
                                        <td>Dr Andrea Saxel<br/>
                                            <em class="small">Cranleigh Senior DSL</em></td>
                                        <td><a href="tel:+447810026050">07810 026050</a></td>
                                        <td><a href="mailto:aps@cranleigh.org">aps@cranleigh.org</a></td>
                                    </tr>
                                    <tr>
                                        <td>Mrs Emma Lewis<br/>
                                            <em class="small">Cranleigh Prep DSL</em></td>
                                        <td><a href="tel:+447810007922">07810 007922</a></td>
                                        <td><a href="mailto:efl@cranleigh.org">efl@cranleigh.org</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <p>Read more about our safeguarding team on the website: <br/><span class="float-end"><a
                                        href="https://www.cranleigh.org/welcome/people/safeguarding" target="_blank">Senior <span class="visually-hidden">(opens in a new tab)</span></a> | <a href="https://www.cranprep.org/welcome/people/safeguarding"
                                                    target="_blank">Prep <span class="visually-hidden">(opens in a new tab)</span></a></span></p>
                        </div>
                    </div>
                    <div class="spacer">&nbsp;</div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Useful School Links</h4>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><a href="https://cranleigh.fireflycloud.net/teen-and-parent-resources"
                                       target="_blank">Cranleigh's Teen and Parent Resources</a></li>
                                <li><a href="https://www.cranleigh.org/about-us/policies/"
                                       target="_blank">Read our School Policies</a></li>
                            </ul>
                        </div>
                    </div>
                    @if(\Illuminate\Support\Facades\Route::is('raiseaconcern.login'))
                    <p class="lead pt-4 d-none d-xl-block">In most cases it's best if we know who is raising the concern, in case there are
                        further details we need to in order keep someone safe.</p><p class="lead pt-2 d-none d-xl-block">However, if you really want to remain anonymous <a
                            href="{{ route('raiseaconcern.submit') }}">you can do that too.</a></p>
                        @endif
                </aside>
            </div>
            <div class="spacer">&nbsp;</div>

            <div class="row">
                <div class="col-md-12">
                    <aside class="card" id="useful-links-card">
                        <div class="card-header">
                            <h4 class="card-title">Useful External Links</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <ul>
                                        <li><a href="https://www.ceop.police.uk/Safety-Centre/" target="_blank">CEOP Safety Centre</a> Report concerns about online abuse directly to the police</li>
                                        <li><a href="https://www.mariecollinsfoundation.org.uk/what-we-do/working-with-children-and-families" target="_blank">Marie Collins Foundation</a> Support for children and families affected by online abuse</li>
                                        <li><a href="https://www.nspcc.org.uk/" target="_blank">NSPCC</a> Support and advice to help keep every child safe from harm</li>
                                    </ul>
                                </div>

                                <div class="col-md-4">
                                    <ul>
                                        <li><a href="https://www.internetmatters.org/resources/social-media-advice-hub/" target="_blank">Internet Matters</a> Practical advice to help children stay safe on social media</li>
                                        <li><a href="https://www.childline.org.uk" target="_blank">Childline</a> Free, confidential support and advice for children and young people</li>
                                        <li><a href="https://www.youngminds.org.uk" target="_blank">Young Minds</a> Helping young people with their mental health and wellbeing</li>
                                    </ul>
                                </div>

                                <div class="col-md-4">
                                    <ul>
                                        <li><a href="https://www.surreycc.gov.uk/children/contact-childrens-services" target="_blank">Surrey's Children's Single Point of Access (C-SPA)</a> Direct contact details for the Council’s safeguarding team</li>
                                        <li><a href="https://saferinternet.org.uk" target="_blank">UK Safer Internet Centre</a> Expert advice and resources to help young people stay safe online</li>
                                        <li><a href="https://www.minded.org.uk" target="_blank">MindEd</a> Free guidance and training for adults supporting young people’s mental health</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
    <footer class="">
        <div class="container-fluid bg-cranleigh text-white raiseaconcern-container py-4">
            <div class="row">
                <div class="col-7">Cranleigh's Raise a Concern and Pastoral Module are both bespoke systems designed and
                    developed at Cranleigh. <br/>Any technical queries should be directed to <a
                        href="mailto:servicedesk@cranleigh.org">the developer</a>.
                </div>
                <div class="col-5"><span class="badge bg-secondary float-end" aria-hidden="true">{{ getAppVersion() }}</span></div>
            </div>
        </div>
    </footer>
</div>
</body>
<script type="text/javascript" nonce="{{ csp_nonce() }}">
    var targetContainer = document.getElementById('notified-container');
    var target = document.getElementById('whogetsnotified');
    var head = document.getElementById('person_type_head');
    var staff = document.getElementById('person_type_staff');
    var pupil = document.getElementById('person_type_pupils');
    var btn = document.getElementsByClassName('btn-check');
    var senior = document.getElementById('school_cs');
    var prep = document.getElementById('school_prep');
    var unknownSchool = document.getElementById('school_unknown');

    function action() {
        if (pupil.checked) {
            if (senior.checked) {
                target.innerHTML = "The safeguarding team at Cranleigh School.";
            } else if (prep.checked) {
                target.innerHTML = 'The safeguarding team at Cranleigh Prep School.';
            } else {
                target.innerHTML = "The safeguarding team.";
            }
        }
        if (staff.checked) {
            if (senior.checked) {
                target.innerHTML = "The Head at Cranleigh School, {{ config('people.CS_HEAD') }}.";
            } else if (prep.checked) {
                target.innerHTML = 'The Head at Cranleigh Prep School, {{ config('people.CPS_HEAD') }}.';
            } else {
                target.innerHTML = "The Head.";
            }
        }
        if (head.checked) {
            target.innerHTML = "The Chair of Governors, {{ config('people.CHAIR_OF_GOVERNORS') }}.";
        }
        if (pupil.checked || head.checked || staff.checked) {
            targetContainer.classList.remove('visually-hidden');
        }

    }

    for (var i = 0; i < btn.length; i++) {
        btn[i].addEventListener('click', action);
    }
</script>
</html>
