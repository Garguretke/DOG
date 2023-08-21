<div class="{{$size_class}} input_height">
	@include('customers.customer-input', [
		'idName' => 'customer_select_klientID',
		'customer_select_id' => $order_customer_id,
		'required' => ($required == 1 ? true : false),
		'withEmptyCustomer' => $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'],
		'selectName' => 'klientID',
		'selectLabel' => 'Kontrahent',
		'block' => (
			(isset($tools) && $tools->count() > 0)
		)
	])
</div>
