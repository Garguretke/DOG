<div class="{{$size_class}} input_height">
	<label for="tool_address">Adres urządzenia</label>
	<div class="input-group">
		<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderDeviceAddress" name="tool_address" pattern=".*\S.*" type="text" class="tool_address_auto_fill form-control {{ $errors->has('tool_address') ? ' is-invalid' : '' }}" value="{{isset($order->tool_address) ? $order->tool_address : old('tool_address')}}" maxlength="250">
		<div class="input-group-append google_map_button"><i class="fal fa-location-dot"></i> Pokaż</div>
	</div>
</div>
