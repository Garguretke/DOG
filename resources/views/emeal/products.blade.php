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
                    <div class="form-group">
                        <label for="name">Nazwa:</label>
                        <input form="addProducteMeal" type="text" name="name" class="form-control" required>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="quantity">Ilość:</label>
                        <input form="addProducteMeal" type="number" name="quantity" class="form-control" required>
                    </div>
                    </br>
                    <button form="addProducteMeal" type="submit" class="btn btn-primary btn-dark">Dodaj produkt</button>

                    <hr>

                    <table
                    id="table"
                    data-toggle="table"
                    data-url="{{ route('emeal.products-store') }}"
                    data-pagination="false">
                    <thead>
                        <tr>
                            <th style= "width:30%" data-field="name">Nazwa</th>
                            <th data-field="quantity">Ilość</th>
                        </tr>
                    </thead>
                    </table>
                </div>

                <form id="addProducteMeal" method="POST" action="{{ route('emeal.products-add') }}">
                    @csrf
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
