<div class="{{$class}}">
	<form method="POST" class="submit-form orderSaveForm input_fix_container" id="orderSaveForm" onsubmit="DisableSaveButton()">

		@if($ustawieniaGlobalne->service_parts_and_operations == 1)
			<div class="card mb-15">
				<div class="card-body no-padding">
					<ul class="nav nav-tabs settings_sections">
						<li class="kontrahent-tab active"><a data-toggle="tab" href="#order_tab_0">Ogólne</a></li>
						@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
							<li class="kontrahent-tab orders_bsf_hidden"><a data-toggle="tab" href="#order_tab_1">Części</a></li>
						@endif
						@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
							<li class="kontrahent-tab orders_bsf_hidden"><a data-toggle="tab" href="#order_tab_2">Czynności</a></li>
						@endif
						@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Wartosc'] == 1)
							<li class="kontrahent-tab orders_bsf_hidden"><a data-toggle="tab" href="#order_tab_3">Wartość zlecenia</a></li>
						@endif
					</ul>
				</div>
			</div>

			<div class="tab-content">
				<div id="order_tab_0" class="tab-pane fade in active">
					<div class="card">
						<div class="card-body">
							@include('orders.form-partial')
						</div>
					</div>
				</div>

				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
					<div id="order_tab_1" class="tab-pane fade">
						<div class="card">
							<div class="card-body">
								@include('orders.service-parts')
							</div>
						</div>
					</div>
				@endif

				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
					<div id="order_tab_2" class="tab-pane fade">
						<div class="card">
							<div class="card-body">
								@include('orders.service-operations')
							</div>
						</div>
					</div>
				@endif

				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Wartosc'] == 1)
					<div id="order_tab_3" class="tab-pane fade">
						<div class="card">
							<div class="card-body">
								@include('orders.service-cost')
							</div>
						</div>
					</div>
				@endif
			</div>
		@elseif($ustawieniaGlobalne->service_parts_and_operations == 2)
			<div class="card">
				<div class="card-body">
					@include('orders.form-partial')
				</div>
			</div>
			<br>
			<div class="orders_bsf_hidden">
				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
					@include('partials.collapsible-card', [
						'title' => 'Części',
						'content' => 'orders.service-parts',
						'save' => true,
						'cacheKey' => 'orders_service_parts',
						'headerRight' => 'orders.add-button-part'
					])
				@endif

				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
					@include('partials.collapsible-card', [
						'title' => 'Czynności',
						'content' => 'orders.service-operations',
						'save' => true,
						'cacheKey' => 'orders_service_operations',
						'headerRight' => 'orders.add-button-operation'
					])
				@endif

				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Wartosc'] == 1)
					@include('partials.collapsible-card', [
						'title' => 'Wartość zlecenia',
						'content' => 'orders.service-cost',
						'save' => true,
						'cacheKey' => 'orders_service_cost',
					])
				@endif
			</div>
		@else
			<div class="card">
				<div class="card-body">
					@include('orders.form-partial')
				</div>
			</div>
		@endif

		<input class="replace_order_input" id="replace_order_input_delete" type="hidden" name="to_finish" value="0">
	</form>

	@if(isset($order->id))
		@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Zakanczanie'] == 1)
			@if($order->finishData == '')
				<form id="finish_form_{{$order->id}}" onsubmit="event.preventDefault();ReplaceOrderForm('delete')"></form>
			@endif
		@endif
	@endif
</div>
<script>
	function ReplaceOrderForm(special_action){
		$(".replace_order_input").val("0");
		$("#replace_order_input_"+special_action).val("1");
		$(".orderSaveButton").trigger('click');
	}
</script>
