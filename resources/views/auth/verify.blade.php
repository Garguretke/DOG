@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (app()->getLocale() === 'en')
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>
                @elseif (app()->getLocale() === 'pl')
                <div class="card-header">{{ __('Zweryfikuj swój adres email') }}</div>
                @endif


                <div class="card-body">
                    @if (session('resent'))
                        @if (app()->getLocale() === 'en')
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @elseif (app()->getLocale() === 'pl')
                            <div class="alert alert-success" role="alert">
                                {{ __('Link weryfikacyjny został wysłany na Twój adres email.') }}
                            </div>
                        @endif
                    @endif
                        @if (app()->getLocale() === 'en')
                            {{ __('Before proceeding, please check your email for a verification link.') }}
                            {{ __('If you did not receive the email') }},
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link btn-dark p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                            </form>
                        @elseif (app()->getLocale() === 'pl')
                            {{ __('Zanim kontynuujesz sprawdź swoją skrzynkę, wysłaliśmy do Ciebie link weryfikacyjny.') }}
                            {{ __('Jeżeli nie otrzymałeś wiadomości email') }},
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('kliknij tutaj aby wysłać ponownie.') }}</button>.
                            </form>
                        @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
