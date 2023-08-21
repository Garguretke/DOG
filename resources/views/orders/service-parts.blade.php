<div class="row">
	@if($ustawieniaGlobalne->service_parts_and_operations == 1)
		<div class="col-xs-12">
			<button id="addOrderServicePartButton" type="button" class="btn btn-success btn-xs pull-right">
				<i class="fal fa-plus-square mr-0"></i>
			</button>
		</div>
	@endif
	<div class="col-xs-12">
		<div id="order_service_parts_content">
			@include('order-service-parts.content')
		</div>
	</div>
</div>
