<div class="card-body">
	<div class="row">
		<div class="col-md-12">
			@if(isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'add')
				<div class="btn btn-success bootstrap_group_operation_button" id="bs_multi_project_add_order" style="width:180px">
					<i class="fal fa-plus-square mr-0"></i>
					Dodaj do projektu
				</div>
			@endif
		</div>
	</div>
</div>
