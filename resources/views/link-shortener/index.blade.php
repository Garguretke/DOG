@extends('layouts.app')

@section('title', 'Link Shortener')

@section('content')
    <div class="container">
        <h3>Link Shortener</h3>
 
        <form action="{{ route('shorten.index') }}" method="post">
            @csrf
            <label for="original_url">Enter a URL:</label>
            <input type="url" name="original_url" id="original_url" required>
            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>
    @if(session('shortened_url'))
        <div class="mt-4">
            <p>Your shortened URL:</p>
            <a href="{{ session('shortened_url') }}" target="_blank">{{ session('shortened_url') }}</a>
        </div>

    @endif

    </div>
@endsection
