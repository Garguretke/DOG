@extends('layouts.app')


@section('content')
    <div class="container">
        <h3>Generator QR Code</h3>

    <form action="{{ route('qrcode.generate') }}" method="post">
        @csrf
        <label for="content">Wprowadź tekst:</label>
        <input type="text" name="content" id="content" value="{{ old('content') }}">
        <button type="submit" class="btn btn-primary">Generuj kod QR</button>

        @error('content')
          <div class="error">{{ $message }}</div>
        @enderror
    </form>


        @if(isset($qrCode))
            <div class="mt-4">
                <img src="data:image/webp;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
            </div>
        @endif
    </div>
@endsection