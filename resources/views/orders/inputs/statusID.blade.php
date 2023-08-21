@php
	$orders_status_types = \App\Models\StatusTypes::where('for_orders','1');
	if(isset($order->statusID)){
		$orders_status_types = $orders_status_types->withTrashed()->where(function($query) use ($order){
			$query->whereNull('deleted_at');
			$query->orWhere('id',$order->statusID);
		});
	}
	if(!is_null($bsf_type_id ?? null)){
		$bsf_definition = \App\Models\OrderBsfType::withTrashed()->where('id',$bsf_type_id)->first();
		if(!is_null($bsf_definition)){
			$bsf_selected_status = $order->statusID ?? 0;
			$bsf_statuses = json_decode($bsf_definition->statuses ?? '[]',true) ?? [];
			$orders_status_types = $orders_status_types->where(function($query) use ($bsf_definition,$bsf_selected_status,$bsf_statuses){
				$query->whereIn('id',$bsf_statuses);
				$query->orWhere('id',$bsf_selected_status);
			});
			if(!empty($bsf_statuses)) $orders_status_types = $orders_status_types->orderByRaw("FIELD(id, ".implode(',', $bsf_statuses).") ASC");
		}
	}
	$orders_status_types = $orders_status_types->orderBy('for_orders_o')->get();
@endphp
<div class="{{$size_class}} input_height">
	<label for="statusID">Status</label>
	<select {{$form_lock}} id="orderStatusInput" name="statusID" class="sel2 form-control {{ $errors->has('statusID') ? 'is-invalid' : '' }}" {{$required == 1 ? 'required' : ''}}>
		<option></option>
		@foreach($orders_status_types as $status)
			<option data-icon="{{$status->icon}}" value="{{$status->id}}" {{$userSelectedStatus == $status->id ? 'selected' : ''}}>{{ $status->name }}</option>
		@endforeach
	</select>
</div>
