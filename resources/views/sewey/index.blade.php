@extends('layouts.app')

@section('title', 'Sewey')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Sewey') }}</div>
                <div class="card-body mb-0">
                    <video width="100%" height="100%" autoplay controls loop>
                    <source src="{{ asset('source/video/muchasgracias.mp4') }}" type="video/mp4">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection