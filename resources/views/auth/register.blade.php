@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header">{{ __('Register') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header">{{ __('Rejestracja') }}</div>
                @endif

                <div class="card-body mb-3">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            @if (app()->getLocale() === 'en')
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                            @elseif (app()->getLocale() === 'pl')
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nazwa użytkownika') }}</label>
                            @endif

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            @if (app()->getLocale() === 'en')
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            @elseif (app()->getLocale() === 'pl')
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Adres Email') }}</label>
                            @endif

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            @if (app()->getLocale() === 'en')
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                            @elseif (app()->getLocale() === 'pl')
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Potwierdź hasło') }}</label>
                            @endif

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-xl-4">
                                @if (app()->getLocale() === 'en')
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                @elseif (app()->getLocale() === 'pl')
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Zarejestruj') }}
                                    </button>
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
