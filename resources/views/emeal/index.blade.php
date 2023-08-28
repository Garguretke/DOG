@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark-subtle">
                <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
                </div>
            </div>
        </nav>        
                </div>

                <div class="card-body bg-dark-subtle">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
