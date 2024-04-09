<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Self Reflections - {{ $pageTitle ?? 'Cranleigh' }}</title>

    <!-- Scripts -->
    @vite([
    'resources/js/app.js',
    'resources/sass/app.scss'
    ])

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="//fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @include('partials.tinymce')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                Self Reflections
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('selfreflection.home') }}">Home</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        <a href="{{ route('selfreflection.login') }}" class="btn btn-cranprep me-2" type="button">Log
                            In</a>
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
                @if (auth()->user())
                    <form class="d-flex" role="logout" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-danger" type="submit">Logout</button>
                    </form>
                @endif
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container img-thumbnail bg-white self-reflection-card">
            <div class="row">
                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('from-pastoral-alert'))
                        <div class="alert alert-warning">
                            <p><strong>Warning</strong></p>
                            <p>{{ session()->pull('from-pastoral-alert') }}</p>
                        </div>
                    @endif
                    {{ displayAlertMsg() }}
                </div>
                <div class="col-12">
                    {{ $slot }}
                </div>
            </div>
        </div>

    </main>
    <footer class="py-4">
        <div class="container img-thumbnail bg-cranleigh text-white">
            <div class="row">
                <div class="col-7">Cranleigh's Self Reflection and Pastoral Module are bespokes system designed and
                    developed at Cranleigh. <br/>Any technical queries should be directed to <a
                        href="mailto:frb@cranleigh.org">the developer</a>.
                </div>
                <div class="col-5"><span class="badge bg-secondary float-end">{{ getAppVersion() }}</span></div>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
