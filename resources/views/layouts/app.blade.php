<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MercjaDOG') }}</title>

    <!-- Fonts & Scripts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app2.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}"></script>
    <link href="{{ asset('fontawesome6/css/all.min.css') }}" rel="stylesheet">
    <script src="{{ asset('select2/dist/js/select2.min.js') }}"></script>
	<link href="{{ asset('select2/dist/css/select2.min.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'MercjaDOG') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                    <ul class="navbar-nav me-auto">
                        @if (Route::has('sewey'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sewey') }}">{{ __('Sewey') }}</a>
                        </li>
                        @endif
                        @if (Route::has('liqcalc'))
                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('liqcalc') }}">{{ __('Liquid Calc') }}</a>
                        </li>
                        @endif

                        @if (Route::has('qrcode.generator'))
                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('qrcode.generator') }}">{{ __('Generator QR') }}</a>
                        </li>
                        @endif

                        @if (Route::has('emeal_index'))
                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('emeal_index') }}">{{ __('eMeal') }}</a>
                        </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('Player') }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-dark-subtle" aria-labelledby="navbarDropdown">
                            @if (Route::has('naruto'))    
                            <a class="dropdown-item bg-dark-subtle" href="{{ route('player', ['series' => 'naruto']) }}">
                                {{ __('Naruto') }}
                            </a>
                            @endif
                            @if (Route::has('shippuuden'))
                            <a class="dropdown-item bg-dark-subtle" href="{{ route('player', ['series' => 'shippuuden']) }}">
                                {{ __('Shippuuden') }}
                            </a>
                            @endif
                            @if (Route::has('boruto'))
                            <a class="dropdown-item bg-dark-subtle" href="{{ route('player', ['series' => 'boruto']) }}">
                                {{ __('Boruto') }}
                            </a>
                            @endif
                        </li>
                    </ul>
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                @if (app()->getLocale() === 'en')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @elseif (app()->getLocale() === 'pl')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Logowanie') }}</a>
                                    </li>
                                @endif
                            @endif

                            @if (Route::has('register'))
                                @if (app()->getLocale() === 'en')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @elseif (app()->getLocale() === 'pl')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Rejestracja') }}</a>
                                    </li>
                                @endif
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end bg-dark-subtle" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item bg-dark-subtle" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        @if (app()->getLocale() === 'en')
                                            {{ __('Logout') }}
                                        @elseif (app()->getLocale() === 'pl')
                                            {{ __('Wyloguj się') }}
                                        @endif
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

        <main >
            @yield('content')
        </main>
    </div>
</body>
</html>
