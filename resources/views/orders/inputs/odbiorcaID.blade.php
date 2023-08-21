<div class="{{$size_class}} input_height">
	@include('customers.customer-input', [
		'idName' => 'customer_select_odbiorcaID',
		'customer_select_id' => $order_recipient_id,
		'required' => ($required == 1 ? true : false),
		'withEmptyCustomer' => $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'],
		'selectName' => 'odbiorcaID',
		'selectLabel' => 'Odbiorca',
		'customer_select_extra_class' => 'ignore_customer_change'
	])
</div>
