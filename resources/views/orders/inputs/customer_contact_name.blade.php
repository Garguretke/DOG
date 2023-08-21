@php
	if(isset($order->customer_contact_name)){
		$order_customer_contact_name = $order->customer_contact_name;
	} else if(isset($customer_contact_name)){
		$order_customer_contact_name = $customer_contact_name;
	} else {
		$order_customer_contact_name = (old('customer_contact_name') != '' ? old('customer_contact_name') : 'Brak');
	}
	$decoded_name = $order_customer_contact_name;
	$decoded_name_pos = strpos($decoded_name,"|");
	if($decoded_name_pos !== false) $decoded_name = substr($decoded_name,$decoded_name_pos+1);
@endphp
<div class="{{$size_class}} input_height">
	<label for="customer_contact_name">Osoba kontaktowa</label>
	@if($uprawnieniaUzytkownika['access_Terminarz_OsobyKontaktowe_Tworzenie'])
		<div style="display:flex;">
			<div style="width:calc(100% - 32px)">
				<select {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderContactName" name="customer_contact_name" class="form-control">
					@if(isset($order))
						<option value="{{$order_customer_contact_name}}">{{$order_customer_contact_name}}</option>
						<script>$(function(){ $("#select2-orderContactName-container").html('{{$decoded_name}}').attr('title','{{$decoded_name}}') })</script>
					@else
						@if($order_customer_contact_name == 'Brak')
							<option id="orders_people_default_option" value="Brak">Brak</option>
						@else
							<option value="{{$order_customer_contact_name}}">{{$order_customer_contact_name}}</option>
							<script>$(function(){ $("#select2-orderContactName-container").html('{{$decoded_name}}').attr('title','{{$decoded_name}}') })</script>
						@endif
					@endif
				</select>
			</div>
			<div style="width:32px">
				<button type="button" class="btn btn-success btn-xs text-center saveOrderPerson saveDynamicPerson" style="height:32px;width:100%" disabled>
					<i class="fal fa-save"></i>
				</button>
			</div>
		</div>
	@else
		<select {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderContactName" name="customer_contact_name" class="form-control">
			@if(isset($order))
				<option value="{{$order_customer_contact_name}}">{{$order_customer_contact_name}}</option>
				<script>$(function(){ $("#select2-orderContactName-container").html('{{$decoded_name}}').attr('title','{{$decoded_name}}') })</script>
			@else
				@if($order_customer_contact_name == 'Brak')
					<option id="orders_people_default_option" value="Brak">Brak</option>
				@else
					<option value="{{$order_customer_contact_name}}">{{$order_customer_contact_name}}</option>
					<script>$(function(){ $("#select2-orderContactName-container").html('{{$decoded_name}}').attr('title','{{$decoded_name}}') })</script>
				@endif
			@endif
		</select>
	@endif
</div>
