@if($ModuleSettings->modul_serwis && $ModuleSettings->modul_serwis_urzadzenia)
	<div class="{{$size_class}} input_height">
		<label for="tool_id">Urządzenie @if($daneUzytkownikaAuth->IsInternalUser() && $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'])<span class="handCursor clearorderDeviceInput">(wyczyść)</span>@endif</label>
		<select {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderDeviceInput" name="tool_id" class="zaladujUrzadzenia form-control {{ $errors->has('tool_id') ? 'is-invalid' : '' }}">
			@if(isset($tools))
				@foreach($tools as $ord)
					<option value="{{$ord->id}}" {{(isset($order->tool_id) && $order->tool_id == $ord->id) || old('tool_id') == $ord->id ? 'selected' : ''}}>#{{$ord->id}} [{{$ord->code}}] {{$ord->name}}</option>
				@endforeach
			@endif
		</select>
	</div>
@endif
