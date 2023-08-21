<div class="{{$size_class}} input_height">
	<label for="planowanaDataRozpoczecia">Data rozpoczęcia</label>
	<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderStartInput" name="planowanaDataRozpoczecia" type="date" min="1970-01-01" max="9999-12-31" class="form-control {{ $errors->has('planowanaDataRozpoczecia') ? 'is-invalid' : '' }}" value="{{isset($order->planowanaDataRozpoczecia) ? \Carbon\Carbon::parse($order->planowanaDataRozpoczecia)->format('Y-m-d') : old('planowanaDataRozpoczecia')}}">
</div>
