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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/source/js/csrf.js') }}"></script>
    <script type="module" src="{{ asset('/plugins/bootstrap/js/bootstrap.js') }}"></script>
    <script type="module" src="{{ asset('/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-table/dist/bootstrap-table.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome6/css/all.css') }}">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'MercjaDOG') }}
                </a>


                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                    <ul class="navbar-nav me-auto">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sewey.get-index') }}">{{ __('Sewey') }}</a>
                        </li>

                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('liqcalc.get-index') }}">{{ __('Liquid Calc') }}</a>
                        </li>

                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('qrcode.get-index') }}">{{ __('Generator QR') }}</a>
                        </li>
                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('emeal.get-index') }}">{{ __('eMeal') }}</a>
                        </li>

                        <li class="nav-item dropdown dropdown-hover">
                            <a id="navbarDropdownPlayer" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown-hover" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('Player') }}
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end bg-dark-subtle" aria-labelledby="navbarDropdownPlayer">
                                <a class="dropdown-item bg-dark-subtle" href="{{ route('player.get-index', ['series' => 'player.naruto']) }}">
                                    {{ __('Naruto') }}
                                </a>
                        
                                <a class="dropdown-item bg-dark-subtle" href="{{ route('player.get-index', ['series' => 'player.shippuuden']) }}">
                                    {{ __('Shippuuden') }}
                                </a>
                        
                                <a class="dropdown-item bg-dark-subtle" href="{{ route('player.get-index', ['series' => 'player.boruto']) }}">
                                    {{ __('Boruto') }}
                                </a>
                            </div>
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
                            <li class="nav-item dropdown dropdown-hover">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" data-hover="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
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
        <style>
            /* Ustawienie dropdown menu jako niewidoczne */
            .dropdown-menu {
                display: none;
            }
        
            /* Pokazanie dropdown menu, gdy nad nim jest myszka */
            .nav-item:hover .dropdown-menu {
                display: block;
            }
        </style>
        <main >
            @yield('content')
        </main>
    </div>
</body>
</html>
