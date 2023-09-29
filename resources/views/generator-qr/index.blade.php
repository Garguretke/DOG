@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Generator QR Code') }}</div>

                <div class="card-body mb-3">
                    <form action="{{ route('qrcode.generate') }}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text">Wprowadź tekst:</span>
                            <input class="form-control" type="text" name="content" id="content" value="{{ old('content') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Generuj kod QR</button>
                        </div>

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