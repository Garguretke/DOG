@php $bsf_comments = \App\Models\BootstrapForm::select('hidden')->where('section_name','service_service_orders')->where('attribute_name','comments')->first(); @endphp

<script>
	var OrderEMU = {
		'chat_timer' : undefined,
		'photos' : [],
		'state' : {
			'modal_edit' : {{ isset($order->id) && !isset($duplikuj) ? 1 : 0 }},
			'update' : false,
			'send' : false
		},
		'comments' : {{ $bsf_comments->hidden == 0 ? 1 : 0 }},
		'service_parts_and_operations' : {{ $ustawieniaGlobalne->service_parts_and_operations }},
		'duplicate_id' : {{ $duplikuj ?? 0 }},
		'original' : '{{ base64_encode(json_encode($order ?? [])) }}',
		'locked' : {{ $form_lock != '' ? 1 : 0 }},
	};
</script>

<script src="{{ asset('scripts/emu-orders.js?v='.$ApplicationVersion) }}"></script>

<script>
	$(function(){
		@if(isset($_GET['trigger_modal_parts']))
			OrderServiceParts_OpenModal(0,OrderServiceParts_BindModalSave,{{ $_GET['product_id'] }},{{ $_GET['price_id'] }},{{ $_GET['warehouse_id'] }});
		@endif
		@if(isset($_GET['trigger_modal_operations']))
			OrderServiceOperations_OpenModal(0,OrderServiceOperations_BindModalSave,{{ $_GET['product_id'] }},{{ $_GET['price_id'] }},'{{ $_GET['operation_attachments_to_save'] ?? '' }}');
		@endif
	});
</script>
