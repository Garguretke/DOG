<div class="row">
	@if($ustawieniaGlobalne->service_parts_and_operations == 1)
		<div class="col-xs-12">
			<button id="addOrderServiceOperationButton" type="button" class="btn btn-success btn-xs pull-right">
				<i class="fal fa-plus-square mr-0"></i>
			</button>
		</div>
	@endif
	<div class="col-xs-12">
		<div id="order_service_operations_content">
			@include('order-service-operations.content')
		</div>
	</div>
</div>
