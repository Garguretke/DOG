@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header bg-dark-subtle">{{ __('Reset Password') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header bg-dark-subtle">{{ __('Reset Hasła') }}</div>
                @endif

                <div class="card-body bg-dark-subtle">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
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

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                @if (app()->getLocale() === 'en')
                                    <button type="submit" class="btn btn-primary btn-dark">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                @elseif (app()->getLocale() === 'pl')
                                    <button type="submit" class="btn btn-primary btn-dark">
                                        {{ __('Wyślij link') }}
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
