<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MercjaDOG') }}</title>

    <!-- Stylesheets -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=Nunito">
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-table/bootstrap-table.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome6/css/all.css') }}">

</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.js"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('/source/js/csrf.js') }}"></script>

    <div id="app">
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'MercjaDOG') }}
                </a>


                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                    <ul class="navbar-nav me-auto">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sewey.get-index') }}"><i class="fal fa-circle-7 fa-xl"></i></a>
                        </li>

                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('liqcalc.get-index') }}"><i class="fal fa-flask fa-xl"></i></a>
                        </li>

                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('qrcode.get-index') }}"><i class="fal fa-qrcode fa-xl"></i></a>
                        </li>
                        <li class="navbar-nav me-auto">
                            <a class="nav-link" href="{{ route('emeal.get-index') }}"><i class="fal fa-burger-soda fa-xl"></i></a>
                        </li>

                        <li class="nav-item dropdown dropdown-hover">
                            <a id="navbarDropdownPlayer" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown-hover" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fal fa-tv-retro fa-xl"></i>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownPlayer">
                                <a class="dropdown-item" href="{{ route('player.get-index', ['series' => 'player.naruto']) }}">
                                    {{ __('Naruto') }}
                                </a>
                        
                                <a class="dropdown-item" href="{{ route('player.get-index', ['series' => 'player.shippuuden']) }}">
                                    {{ __('Shippuuden') }}
                                </a>
                        
                                <a class="dropdown-item" href="{{ route('player.get-index', ['series' => 'player.boruto']) }}">
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

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
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

            .socialmedia {
                margin: 0 10px;
                border-radius: 50%;
                box-sizing: border-box;
                width: 100px;
                height: 100px;
                float: center;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                text-decoration: none;
                transition: 0.5s;
                background: rgba(0, 0, 0, 0);
                color: var(--color);
                font-size: 2.5em;
                --webkit-box-reflect: below 5px linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4));
            }

            .socialmedia:hover {
                background: var(--color);
                color: #fff;
                box-shadow: 0 0 5px var(--color),
                    0 0 25px var(--color),
                    0 0 50px var(--color),
                    0 0 100px var(--color);
            }

			::-webkit-scrollbar {
				width: 10px;
				height: 10px;
			}

			::-webkit-scrollbar-thumb {
				background: #888;
				border-radius: 50px;
			}

			:::-webkit-scrollbar-track {
				background: transparent;
			}
        </style>
        <main>
            @yield('content')
        </main>
    </div>
    <footer class="bd-footer py-4 py-md-5 mt-auto bg-body-tertiary">
        <div class="container text-center px-4 px-md-3 text-body-secondary">
            <a class="socialmedia" href="#" target=”_blank” style="--color: #0072b1">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
            <a class="socialmedia" href="https://github.com/Garguretke" target=”_blank” style="--color: #171515">
                <i class="fa-brands fa-github"></i>
            </a>
            <a class="socialmedia" href="#" target=”_blank” style="--color: #5865F2">
                <i class="fa-brands fa-discord"></i>
            </a>
            <a class="socialmedia" href="#" target=”_blank” style="--color: #000000">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a class="socialmedia" href="#" target=”_blank” style="--color: #E1306C">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a class="socialmedia" href="#" target=”_blank” style="--color: #4267B2">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
        </div>
    </footer>
</body>
</html>
