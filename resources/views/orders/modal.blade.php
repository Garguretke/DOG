<div class="modal" id="orderModal" data-hidescroll="true" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
	<form id="orderModalForm" method="POST" data-create-route="{{route('orders.post-create')}}" enctype="multipart/form-data" class="orderSaveForm submit-form input_fix_container" data-dz-callback="Order_AfterUploadFiles">
		@csrf
		<input class="replace_order_input" id="replace_order_input_to_finish" type="hidden" name="to_finish" value="0">

		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content" id="OrderModalContent"></div>
		</div>
	</form>

	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Zakanczanie'] == 1)
		<form action="" id="order_finish_form_modal" onsubmit="event.preventDefault();ReplaceOrderForm('to_finish')"></form>
	@endif
</div>

<script>
	function ReplaceOrderForm(special_action){
		$(".replace_order_input").val("0");
		$("#replace_order_input_"+special_action).val("1");
		$(".orderSaveButton").trigger('click');
	}
</script>
