@if(isset($order->id))
	<div class="{{$size_class}} input_height">
		<label>Rodzaj zlecenia</label>
		<input disabled type="text" class="form-control" value="{{ $order->bsf_type->name ?? 'Standardowe' }}">
	</div>
@elseif(!isset($order->id) && isset($bsf_type_id))
	<div class="{{$size_class}} input_height">
		<label>Rodzaj</label>
		<select id="order_bsf_type_id" class="form-control sel2">
			<option></option>
			@foreach(\App\Models\OrderBsfType::whereIn('id',$userBsfTypesAccess['order'])->get() as $type)
				<option value="{{$type->id}}" {{ $type->id == $bsf_type_id ? 'selected' : '' }}>{{ $type->name }}</option>
			@endforeach
		</select>
	</div>
@endif
