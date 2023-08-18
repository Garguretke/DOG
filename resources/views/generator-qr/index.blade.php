@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark-subtle">{{ __('Generator QR Code') }}</div>

                <div class="card-body bg-dark-subtle">
                    <form action="{{ route('qrcode.generate') }}" method="post">
                        @csrf
                        <label for="content">Wprowadź tekst:</label>
                        <input type="text" name="content" id="content" value="{{ old('content') }}">
                        <button type="submit" class="btn btn-primary btn-dark">Generuj kod QR</button>

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
            </div>
        </div>
    </div>
</div>
@endsection