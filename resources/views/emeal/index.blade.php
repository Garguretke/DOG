@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                @if (app()->getLocale() === 'en')
                    <div class="card-header bg-dark-subtle">{{ __('eMeal') }}</div>
                @elseif (app()->getLocale() === 'pl')
                    <div class="card-header bg-dark-subtle">{{ __('eMeal') }}</div>
                @endif

                <div class="card-body bg-dark-subtle">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
