@extends('layouts.app')

@section('title', 'Ranking')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
					<select id="rankingSelect" class="form-select form-select-sm" aria-label="select-ranking-menu" onchange="changeRanking()">
						@foreach ($rankings as $ranking)
						<option value="{{ $ranking->id }}">{{ $ranking->name }}</option>
						@endforeach
					</select>
                </div>        

                <div class="card-body  mb-3">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Launch demo modal
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                            ...
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
		function changeRanking() {

	}
</script>

@endsection
