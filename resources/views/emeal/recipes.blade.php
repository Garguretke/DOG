@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <nav class="navbar navbar-expand-md navbar-dark-subtle">
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto">
                                @if (Route::has('emeal.get-index'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('emeal.get-index') }}">{{ __('eMeal') }}</a>
                                </li>
                                @endif
                                @if (Route::has('emeal.products'))
                                <li class="navbar-nav me-auto">
                                    <a class="nav-link" href="{{ route('emeal.products') }}">{{ __('Produkty') }}</a>
                                </li>
                                @endif

                                @if (Route::has('emeal.recipes'))
                                <li class="navbar-nav me-auto">
                                    <a class="nav-link" href="{{ route('emeal.recipes') }}">{{ __('Przepisy') }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </nav>
                </div>        

                <div class="card-body mb-3">
                    <h1>Lista Przepisów</h1>
                    <ul>
                        @foreach($recipes as $recipe)
                        <div class="list-group">
                            <a class="col-6 list-group-item list-group-item-action list-group-item-dark" href="{{ route('emeal.recipes-show', $recipe->id) }}">{{ $recipe->name }}</a>
                        </div>
                        @endforeach
                    </ul>
                    <a href="{{ route('emeal.recipes-create') }}" class="btn btn-primary btn-dark">Dodaj nowy przepis</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
