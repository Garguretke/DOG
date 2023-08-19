@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header">{{ __('Confirm Password') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header">{{ __('Potwierdź hasło') }}</div>
                @endif


                <div class="card-body">
                @if (app()->getLocale() === 'en')
                    {{ __('Please confirm your password before continuing.') }}
                @elseif (app()->getLocale() === 'pl')
                    {{ __('Proszę potwierdź swoje hasło.') }}
                @endif

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

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

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if (app()->getLocale() === 'en')
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm Password') }}
                                    </button>
                                @elseif (app()->getLocale() === 'pl')
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Potwierdź hasło') }}
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
