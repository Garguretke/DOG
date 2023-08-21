@if($ModuleSettings->modul_serwis && $ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1)
	<div class="{{$size_class}}" id="additionalTasks">
		@if(!isset($order->id))
			<h4>Powiązane zadanie</h4>
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input fix_dla_checka" name="create_task" id="createTaskCheck">
				<label class="form-check-label" for="createTaskCheck">Utwórz zadanie</label>
			</div>
			<br>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 input_height">
					<label>Data rozpoczęcia zadania</label>
					<input id="order_task_date" name="task_date" type="date" min="1970-01-01" max="9999-12-31" class="form-control {{ $errors->has('task_date') ? 'is-invalid' : '' }}" value="{{old('task_date')}}" disabled>
				</div>
				@if($ustawieniaGlobalne->bsf_types_tasks == 1)
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 input_height">
						<label>Rodzaj zadania</label>
						<select id="order_task_bsf" name="task_bsf_type" class="form-control sel2" disabled>
							<option></option>
							@foreach(\App\Models\TaskBsfType::whereIn('id',$userBsfTypesAccess['task'])->get() as $type)
								<option value="{{$type->id}}">{{ $type->name }}</option>
							@endforeach
						</select>
					</div>
				@else
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 input_height">
						<label>Typ zadania</label>
						<select id="order_task_type" name="task_type" class="sel2 form-control {{ $errors->has('event_type') ? 'is-invalid' : '' }}" disabled>
							@foreach(\App\Models\TaskType::orderBy('name','asc')->get() as $type)
								<option value="{{$type->id}}" {{$type->id == $daneUzytkownikaAuth->default_task_type ? 'selected' : ''}}>{{$type->name}}</option>
							@endforeach
						</select>
					</div>
				@endif
			</div>
		@endif
	</div>
@endif
