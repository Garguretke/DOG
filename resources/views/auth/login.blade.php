@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header">{{ __('Login') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header">{{ __('Logowanie') }}</div>
                @endif

                <div class="card-body mb-3">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            @if (app()->getLocale() === 'en')
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            @elseif (app()->getLocale() === 'pl')
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Adres Email') }}</label>
                            @endif

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            @if (app()->getLocale() === 'en')
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            @elseif (app()->getLocale() === 'pl')
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Hasło') }}</label>
                            @endif

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    @if (app()->getLocale() === 'en')
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    @elseif (app()->getLocale() === 'pl')
                                        <label class="form-check-label" for="remember">
                                            {{ __('Zapamiętaj mnie') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if (app()->getLocale() === 'en')
                                    <button type="submit" class="btn btn-primary btn-dark">
                                        {{ __('Login') }}
                                    </button>
                                @elseif (app()->getLocale() === 'pl')
                                    <button type="submit" class="btn btn-primary btn-dark">
                                        {{ __('Zaloguj') }}
                                    </button>
                                @endif

                                @if (Route::has('password.request'))
                                    @if (app()->getLocale() === 'en')
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @elseif (app()->getLocale() === 'pl')
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Zapomniałeś hasła?') }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
