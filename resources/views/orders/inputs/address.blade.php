<div class="{{$size_class}} input_height">
	<label for="address">Adres</label>
	<div class="input-group">
		<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} name="address" pattern=".*\S.*" type="text" class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{isset($order->address) ? $order->address : old('address')}}" maxlength="250">
		<div class="input-group-append google_map_button"><i class="fal fa-location-dot"></i> Pokaż</div>
	</div>
</div>
