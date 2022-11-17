<!doctype html>
{{-- Design Idea: https://www.surreycc.gov.uk/children/contact-childrens-services --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="//cdn.tiny.cloud/1/5aorefy1a3tzggygtpkx81v9k5puvldfm55a0il6y929m3fw/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea.wysiwyg',
            menubar: false,
            statusbar: false,
            browser_spellcheck: true,
            toolbar1: 'undo redo | styleselect | bold italic | link bullist numlist outdent indent | forecolor backcolor | paste',
            plugins: ['paste', 'lists', 'link'],
            contextmenu: [], // we do this so that TinyMCE doesn't overwrite our users right click context menu
            paste_data_images: false,
            paste_word_valid_elements: "p,b,strong,i,em,h1,h2,h3,h4,h5,h6,a,br,ul,ol,li,hr,font,code,del,s",
            paste_webkit_styles: "color",
            paste_retain_style_properties: "color",
            paste_merge_formats: true,
            setup: function (editor) {
                editor.on('blur', function (e) {
                    var content = tinymce.activeEditor.getContent();
                    if (content.includes("<img src=")) {
                        alert("I've noticed your input contains an image. This cannot be saved here. Please save and then add the image as an attachment in notes.");
                    }
                });
            }
        });
    </script>
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container img-thumbnail bg-white" style="border:0px;padding-top:10px; padding-bottom: 10px;">
            <div class="row">
                <div class="col-12">
                    <p class="lead text-danger text-center">If you think that a child is in immediate danger you should call: <a href="tel:999">999</a>.</p>
<hr />
                </div>
                <div class="col-xl-8 col-12">
                    @if (session()->has('from-pastoral-alert'))
                        <div class="alert alert-warning">
                            <p><strong>Warning</strong></p>
                            <p>{{ session()->pull('from-pastoral-alert') }}</p>
                        </div>
                    @endif
                    {{ displayAlertMsg() }}

                    @yield('content')
                </div>
                <div class="col-xl-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Contacts</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
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
                                            <em class="small">CS Designated Safeguarding Lead</em></td>
                                        <td><a href="tel:+447810026050">07810 026050</a></td>
                                        <td><a href="mailto:aps@cranleigh.org">aps@cranleigh.org</a></td>
                                    </tr>
                                    <tr>
                                        <td>Mrs Emma Lewis<br/>
                                            <em class="small">CPS Designated Safeguarding Lead</em></td>
                                        <td><a href="tel:+447810007922">07810 007922</a></td>
                                        <td><a href="mailto:efl@cranprep.org">efl@cranprep.org</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <p>Read more about our safeguarding team on the website: <a href="https://www.cranleigh.org/welcome/people/safeguarding" target="_blank">Cranleigh School</a> | <a href="https://www.cranprep.org/welcome/people/safeguarding" target="_blank">Cranleigh Prep School</a></p>
                        </div>
                    </div>
                    <div class="spacer">&nbsp;</div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Useful Links</h4>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><a href="https://cranleigh.fireflycloud.net/teen-and-parent-resources"
                                       target="_blank">Cranleigh's Teen and Parent Resources</a></li>
                                <li><a href="https://www.cranleigh.org/policies/child-protection-safeguarding/"
                                       target="_blank">Cranleigh School's Safeguarding Policy</a></li>
                                <li><a href="https://www.cranprep.org/policies/safeguarding-child-protection-policy/"
                                       target="_blank">Cranleigh Prep School's Safeguarding Policy</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>
    <footer>

    </footer>
</div>
</body>
<script type="text/javascript">
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
                target.innerHTML = "The Headmaster at Cranleigh School, {{ config('people.CS_HEAD') }}.";
            } else if (prep.checked) {
                target.innerHTML = 'The Headmaster at Cranleigh Prep School, {{ config('people.CPS_HEAD') }}.';
            } else {
                target.innerHTML = "The Headmaster.";
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
