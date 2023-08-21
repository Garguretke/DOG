@php
	$orders_division_types = \App\Models\DivisionTypes::withTrashed()->orderBy('name')->get();

	if(isset($order->dzialID)){
		$order_selected_division_id = $order->dzialID;
	} else if(old('dzialID') != ''){
		$order_selected_division_id = old('dzialID');
	} else {
		$division = \App\Models\Customer::select('divisionID')->where('id',$order_customer_id)->withTrashed()->first();
		if(!is_null($division)){
			$order_selected_division_id = $division->divisionID;
		} else {
			$order_selected_division_id = 0;
		}
	}
@endphp
<div class="{{$size_class}} input_height">
	<label for="dzialID">Grupa kontrahenta</label>
	<select {{$form_lock}} id="orderDzialID" name="dzialID" class="sel2 form-control {{ $errors->has('dzialID') ? 'is-invalid' : '' }}" {{$required == 1 ? 'required' : ''}}>
		<option value="">Wybierz</option>
		@foreach($orders_division_types as $division)
			<option value="{{$division->id}}" {{ $order_selected_division_id == $division->id ? 'selected' : '' }}>{!! $division->name == '' ? '---' : $division->name !!}</option>
		@endforeach
	</select>
</div>
