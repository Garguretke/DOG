@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Notebook') }}</div>

                <div class="card-body mb-3">
                    <table class="table" data-toggle="table" data-pagination="true" data-search="true">
                        <thead>
                            <tr>
                                <th data-width="10" data-width-unit="%">Title</th>
                                <th data-width="10" data-width-unit="%">Type</th>
                                <th data-width="70" data-width-unit="%">Content</th>
                                <th data-width="10" data-width-unit="%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                            <tr>
                                <td>{{ $note->title }}</td>
                                <td>{{ $note->type }}</td>
                                <td>{{ $note->content }}</td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $note->id }}">Delete</button>
                                </td>
                            </tr>
							@include('notebook.notebook-modal-delete')
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Note</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('notebook.notebook-modal-add')

@endsection
