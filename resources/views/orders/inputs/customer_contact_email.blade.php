@php
	if(isset($order->customer_contact_email)){
		$order_customer_contact_email = $order->customer_contact_email;
	} else if(isset($customer_contact_email)){
		$order_customer_contact_email = $customer_contact_email;
	} else {
		$order_customer_contact_email = old('customer_contact_email');
	}
@endphp
<div class="{{$size_class}} input_height">
	<label for="customer_contact_email">E-mail kontaktowy</label>
	<div style="display:flex">
		<div class="contact_telephone_input">
			<input {{$form_lock}} {{$required == 1 ? 'required' : ''}} id="orderContactEmail" name="customer_contact_email" type="email" class="form-control {{$errors->has('customer_contact_email' ? 'is-invalid' : '')}}" value="{{$order_customer_contact_email}}">
		</div>
		<div class="contact_telephone_button">
			<div class="btn btn-success btn-xs text-center" id="order_link_mail">
				<i class="fal fa-envelope mr-0"></i>
			</div>
		</div>
	</div>
</div>
