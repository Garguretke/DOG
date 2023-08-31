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
                                @if (Route::has('emeal_index'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('emeal_index') }}">{{ __('eMeal') }}</a>
                                </li>
                                @endif
                                @if (Route::has('emeal_products'))
                                <li class="navbar-nav me-auto">
                                    <a class="nav-link" href="{{ route('emeal_products') }}">{{ __('Produkty') }}</a>
                                </li>
                                @endif

                                @if (Route::has('emeal_recipes'))
                                <li class="navbar-nav me-auto">
                                    <a class="nav-link" href="{{ route('emeal_recipes') }}">{{ __('Przepisy') }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </nav>
                </div>        

                <div class="card-body bg-dark-subtle">
                    <form method="POST" action="{{ route('emeal_products_store') }}">
                    @csrf
                        <div class="form-group">
                            <label for="name">Nazwa:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        </br>
                        <div class="form-group">
                            <label for="quantity">Ilość:</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        </br>
                        <button type="submit" class="btn btn-primary btn-dark">Dodaj produkt</button>
                    </form>
                    <hr>
                    <!--<table class="table" data-toggle="table" data-pagination="true">
                        <thead>
                            <tr>
                                <th data-field="name">Nazwa</th>
                                <th data-field="quantity">Ilość</th>
                                <th data-field="created_at">Data dodania</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>-->
                    <table
                    id="table"
                    data-toggle="table"
                    data-url="{{ route('emeal_products_store') }}"
                    data-pagination="false">
                    <thead>
                        <tr>
                            <th data-field="name">Nazwa</th>
                            <th data-field="quantity">Ilość</th>
                            <th data-field="created_at">Data dodania</th>
                        </tr>
                    </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
