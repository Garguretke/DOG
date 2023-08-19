@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header bg-dark-subtle">{{ __('Dashboard') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header bg-dark-subtle">{{ __('Strona główna') }}</div>
                @endif

                <div class="card-body bg-dark-subtle">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        @if (app()->getLocale() === 'en')
                            {{ __('You are logged in!') }}
                        @elseif (app()->getLocale() === 'pl')
                            {{ __('Zostałeś zalogowany!') }}
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
