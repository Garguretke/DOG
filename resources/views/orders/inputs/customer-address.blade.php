<div class="{{$size_class}} input_height">
	<label for="orderCustomerAddress">Adres kontrahenta</label>
	<div class="input-group">
		<input disabled id="orderCustomerAddress" type="text" name="address_full" class="form-control" value="{{ $order->klient->address_full ?? '' }}">
		<div class="input-group-append google_map_button"><i class="fal fa-location-dot"></i> Pokaż</div>
	</div>
</div>
