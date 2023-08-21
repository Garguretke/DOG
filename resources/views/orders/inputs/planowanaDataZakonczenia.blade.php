<div class="{{$size_class}} input_height">
	<label for="planowanaDataZakonczenia">Data zakończenia</label>
	<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderEndInput" name="planowanaDataZakonczenia" type="date" min="1970-01-01" max="9999-12-31" class="form-control {{ $errors->has('planowanaDataZakonczenia') ? 'is-invalid' : '' }}" value="{{isset($order->planowanaDataZakonczenia) ? \Carbon\Carbon::parse($order->planowanaDataZakonczenia)->format('Y-m-d') : old('planowanaDataZakonczenia') }}">
</div>
