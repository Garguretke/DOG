@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark-subtle">
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

                <div class="card-body bg-dark-subtle">
                    <h1>{{ __('Edit Recipe') }}: {{ $recipe->name }}</h1>
                    <form method="POST" action="{{ route('emeal.recipes-update', $recipe->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">{{ __('Nazwa Przepisu') }}:</label>
                            <input type="text" name="name" class="form-control" value="{{ $recipe->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('Opis') }}:</label>
                            <textarea name="description" class="form-control">{{ $recipe->description }}</textarea>
                        </div>

                        <!-- Dodawanie produktów do przepisu -->
                        <div class="form-group">
                            <label for="products">{{ __('Produkty') }}:</label>
                            <select name="products[]" class="form-control" multiple>
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}" @if ($recipe->products->contains($product->id)) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Zaktualizuj Przepis') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
