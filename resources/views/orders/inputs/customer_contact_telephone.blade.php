@php
	if(isset($order->customer_contact_telephone)){
		$order_customer_contact_telephone = $order->customer_contact_telephone;
	} else if(isset($customer_contact_telephone)){
		$order_customer_contact_telephone = $customer_contact_telephone;
	} else {
		$order_customer_contact_telephone = old('customer_contact_telephone');
	}
@endphp
<div class="{{$size_class}} input_height">
	<label for="customer_contact_telephone">Telefon kontaktowy</label>
	<div style="display:flex">
		<div class="contact_telephone_input">
			<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderContactPhone" name="customer_contact_telephone" type="text" maxlength="255" class="form-control {{$errors->has('customer_contact_telephone' ? 'is-invalid' : '')}}" value="{{$order_customer_contact_telephone}}">
		</div>
		<div class="contact_telephone_button">
			<div class="btn btn-success btn-xs text-center" id="order_link_telephone">
				<i class="fal fa-phone mr-0"></i>
			</div>
		</div>
	</div>
</div>
