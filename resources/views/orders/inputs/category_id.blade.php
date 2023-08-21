@php
	$orders_category = \App\Models\OrderCategory::orderBy('name');
	if(isset($order->category_id)){
		$orders_category = $orders_category->withTrashed()->where(function($query) use ($order){
			$query->whereNull('deleted_at');
			$query->orWhere('id',$order->category_id);
		});
	}
	$orders_category = $orders_category->get();
@endphp
<div class="{{$size_class}} input_height">
	<label>Kategoria</label>
	<select {{$form_lock}} name="category_id" class="sel2 form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" {{$required == 1 ? 'required' : ''}}>
		<option value="0">Brak</option>
		@foreach($orders_category as $category)
			<option value="{{$category->id}}" {{$userSelectedCategory == $category->id ? 'selected' : ''}}>[{{ $category->code }}] {{ $category->name }}</option>
		@endforeach
	</select>
</div>
