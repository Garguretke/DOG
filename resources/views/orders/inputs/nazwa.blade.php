<div class="{{$size_class}} input_height">
	<label for="nazwa">Nazwa</label>
	<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} name="nazwa" pattern=".*\S.*" id="orderNameInput" type="text" class="form-control {{ $errors->has('nazwa') ? ' is-invalid' : '' }}" value="{{isset($order->nazwa) ? $order->nazwa : old('nazwa')}}" maxlength="250">
</div>
