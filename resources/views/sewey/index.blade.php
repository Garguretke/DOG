@extends('layouts.app')

@section('title', 'Sewey')

@section('content')
    <div class="container">
        <h3>Sewey</h3>
        <video width="100%" height="100%" autoplay controls loop>
            <source src="{{ asset('source/video/muchasgracias.mp4') }}" type="video/mp4">
        </video>
    </div>
@endsection