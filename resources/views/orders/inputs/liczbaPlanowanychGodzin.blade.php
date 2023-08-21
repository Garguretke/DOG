<div class="{{$size_class}} input_height">
	<label for="liczbaPlanowanychGodzin">Liczba planowanych godzin</label>
	<div class="input-group">
			<input {{$form_lock}} name="liczbaPlanowanychGodzin" id="orderPlannedHours" type="number" min="0" step="0.01" class="form-control {{ $errors->has('liczbaPlanowanychGodzin') ? ' is-invalid' : '' }}" value="{{isset($order->liczbaPlanowanychGodzin) ? $order->liczbaPlanowanychGodzin : old('liczbaPlanowanychGodzin')}}" {{$required == 1 ? 'required' : ''}}>
			<div class="input-group-append" style="width:78px;">
					<span class="input-group-text">godz.</span>
			</div>
	</div>
</div>
