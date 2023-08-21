@php
	if(isset($_GET['trigger_load_buffer']) || isset($lupa_order_data_overwrite)){
		$process_lupa = true;
	} else {
		$process_lupa = false;
	}

	$cached_attribute_values = [];

	if($process_lupa){
		$lupa_order_data = $GlobalFunction->getCacheSQL('LUPA_ORDER_DATA','');
		if($lupa_order_data != ''){
			$lupa_order_data = json_decode($lupa_order_data,null,512,JSON_FORCE_OBJECT);
			$GlobalFunction->forgetCacheSQL('LUPA_ORDER_DATA');
			if(!isset($order)) $order = new \App\Models\Orders();
			\App\Http\Controllers\OrdersController::restoreCurrent($order,$lupa_order_data);
			$cached_attribute_values = json_decode($GlobalFunction->getCacheSQL('LUPA_ORDER_DATA_ATTRIBUTES','[]'),true);
			$GlobalFunction->forgetCacheSQL('LUPA_ORDER_DATA_ATTRIBUTES');
			$dz_attachment_to_save = $GlobalFunction->getCacheSQL('LUPA_ORDER_DATA_ATTACHMENTS','[]');
			$GlobalFunction->forgetCacheSQL('LUPA_ORDER_DATA_ATTACHMENTS');
			$clear_order_uri = true;
			if(!is_null($order->tool_id) && $order->tool_id > 0){
				$tools = \App\Models\Tool::withTrashed()->where('id',$order->tool_id)->get();
			}
		}
	}

	if(!isset($users)) $users = \App\Models\User::where('is_technical','0')->where('role','!=','REMOTEUSER')->get();
	if(!isset($divisionTypes)) $divisionTypes = \App\Models\DivisionTypes::get();
	if(!isset($with_attachments)) $with_attachments = true;
	if(!isset($modal)) $modal = false;

	if(isset($_GET['project_id'])){
		$project = \App\Models\eProject::withTrashed()->where('id',$_GET['project_id'])->first();
		$order_customer_id = $project->customer_id ?? 0;
		$order_recipient_id = $project->customer_id ?? 0;
	}

	$order_customer_id = $order_customer_id ?? 0;
	if(isset($order) && !empty($order->klientID)){
		$order_customer_id = $order->klientID;
	} else if(isset($customer)){
		$order_customer_id = $customer->id;
	} else if(isset($zlecenie)){
		$order_customer_id = $zlecenie->klientID;
	} else {
		if(!$daneUzytkownikaAuth->IsInternalUser() && $order_customer_id == 0){
			$order_customer_id = $daneUzytkownikaAuth->customer_id;
		}
	}

	$order_recipient_id = $order_recipient_id ?? 0;
	if(isset($order) && !empty($order->odbiorcaID)){
		$order_recipient_id = $order->odbiorcaID;
	} else if(isset($customer)){
		$order_recipient_id = $customer->id;
	} else if(isset($zlecenie)){
		$order_recipient_id = $zlecenie->odbiorcaID;
	} else {
		if(!$daneUzytkownikaAuth->IsInternalUser()){
			$order_recipient_id = $daneUzytkownikaAuth->customer_id;
		}
	}

	if($process_lupa){
		if(!($order_recipient_id > 0) && $order_customer_id > 0){
			$order_recipient_id = $order_customer_id;
		} else if(!($order_customer_id > 0) && $order_recipient_id > 0){
			$order_customer_id = $order_recipient_id;
		}
	}

	$userSelectedStatus = isset($order->statusID) ? $order->statusID : (old('statusID') != '' ? old('statusID') : $daneUzytkownikaAuth->default_order_status);
	$userSelectedCategory = isset($order->category_id) ? $order->category_id : (old('category_id') != '' ? old('category_id') : null);

	if(isset($duplikuj)) unset($order->id);

	use App\Models\BootstrapForm;
	use App\Models\OrderBsfTypeConfig;
	$bsf_type_id = $order->bsf_type_id ?? ($bsf_type_id ?? null);
	if(!is_null($bsf_type_id)){
		if($daneUzytkownikaAuth->IsInternalUser()){
			$bsf_query = OrderBsfTypeConfig::selectRaw('`attribute_name`, `required`, `size`, `hidden` as `state`')->where('type_id',$bsf_type_id)->where('hidden','!=','1')->orderBy('order_number','ASC');
			$bsf_zalaczniki = OrderBsfTypeConfig::selectRaw("`hidden` as `state`")->where('type_id',$bsf_type_id)->where('attribute_name','attachments')->first();
		} else {
			$bsf_query = OrderBsfTypeConfig::selectRaw('`attribute_name`, `required`, `size`, `hidden_zew` as `state`')->where('type_id',$bsf_type_id)->where('hidden_zew','!=','1')->orderBy('order_number','ASC');
			$bsf_zalaczniki = OrderBsfTypeConfig::selectRaw("`hidden_zew` as `state`")->where('type_id',$bsf_type_id)->where('attribute_name','attachments')->first();
		}
	} else {
		if($daneUzytkownikaAuth->IsInternalUser()){
			$bsf_query = BootstrapForm::selectRaw('`attribute_name`, `required`, `size`, `hidden` as `state`')->where('section_name','service_service_orders')->where('hidden','!=','1')->orderBy('order_number','ASC');
			$bsf_zalaczniki = BootstrapForm::selectRaw("`hidden` as `state`")->where('section_name','service_service_orders')->where('attribute_name','attachments')->first();
		} else {
			$bsf_query = BootstrapForm::selectRaw('`attribute_name`, `required`, `size`, `hidden_zew` as `state`')->where('section_name','service_service_orders')->where('hidden_zew','!=','1')->orderBy('order_number','ASC');
			$bsf_zalaczniki = BootstrapForm::selectRaw("`hidden_zew` as `state`")->where('section_name','service_service_orders')->where('attribute_name','attachments')->first();
		}
	}
	$input_list_down = clone $bsf_query;
	$input_list_down = $input_list_down->where('position','0')->get();

	$input_list_up = clone $bsf_query;
	$input_list_up = $input_list_up->where('position','3')->get();

	$input_list_left = clone $bsf_query;
	$input_list_left = $input_list_left->where('position','1')->get();

	$input_list_right = clone $bsf_query;
	$input_list_right = $input_list_right->where('position','2')->get();

	$size_class = ViewHelper::getAttributeSizeClass();
	$bsf_form_ignore = ['attributes-a','attributes-b','attributes-c','attributes-d','attributes-e'];
@endphp
@csrf
<input type="hidden" name="redirect" value="{{ $redirect ?? 'orders' }}">
<input type="hidden" name="go_to_pdf" value="">
<input type="hidden" name="template_id" value="">
<input type="hidden" id="orderTradeOrderInput" value="{{$order->trade_order_id ?? 0}}">
<input type="hidden" id="orderTaskInput" value="{{$order->task_id ?? 0}}">
<input type="hidden" id="orderEventInput" value="{{$order->event_id ?? 0}}">
<input type="hidden" name="create_from_tradeorder" value="{{$create_from_tradeorder ?? 0}}">
<input type="hidden" name="create_from_task" value="{{$create_from_task ?? 0}}">
<input type="hidden" name="create_from_event" value="{{$create_from_event ?? 0}}">
<input type="hidden" id="OrderServiceOperationCollection" name="order_service_operations_id" value="{{ $order->order_service_operations_id ?? old('order_service_operations_id') }}">
<input type="hidden" id="OrderServicePartCollection" name="order_service_parts_id" value="{{ $order->order_service_parts_id ?? old('order_service_parts_id') }}">
@if($bsf_zalaczniki->state == 1)
	<input type="hidden" id="orderID" value="{{$order->id ?? 0}}">
@endif
<input type="hidden" id="CzatOrderID" value="{{isset($order) ? $order->id : ''}}">
<input type="hidden" id="CzatOrderRoute" value="{{isset($order->id) ? route('order-message.load', ['id' => $order->id]) : ''}}">
<input type="hidden" name="set_to_project" value="{{$_GET['project_id'] ?? 0}}">
<input type="hidden" name="email_id" value="{{$order->email_id ?? 0}}">
<input type="hidden" name="bsf_type_id" value="{{$bsf_type_id}}">

<div class="row">
	@foreach($input_list_up as $input)
		@if(strpos($input->attribute_name,"attribute_") !== false)
			@include('orders.inputs.attributes-template',['attribute_section' => 0,'only_attribute_id' => substr($input->attribute_name,10)])
		@elseif(View::exists('orders.inputs.'.$input->attribute_name))
			@include('orders.inputs.'.$input->attribute_name,[
				'required' => $input->required,
				'size_class' => $size_class[$input->size],
				'form_lock' => $input->state == 2 && !in_array($input->attribute_name,$bsf_form_ignore) ? 'disabled' : $form_lock
			])
		@endif
	@endforeach
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6">
		<div class="row">
			@foreach($input_list_left as $input)
				@if(strpos($input->attribute_name,"attribute_") !== false)
					@include('orders.inputs.attributes-template',['attribute_section' => 0,'only_attribute_id' => substr($input->attribute_name,10)])
				@elseif(View::exists('orders.inputs.'.$input->attribute_name))
					@include('orders.inputs.'.$input->attribute_name,[
						'required' => $input->required,
						'size_class' => $size_class[$input->size],
						'form_lock' => $input->state == 2 && !in_array($input->attribute_name,$bsf_form_ignore) ? 'disabled' : $form_lock
					])
				@endif
			@endforeach
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6">
		<div class="row">
			@foreach($input_list_right as $input)
				@if(strpos($input->attribute_name,"attribute_") !== false)
					@include('orders.inputs.attributes-template',['attribute_section' => 0,'only_attribute_id' => substr($input->attribute_name,10)])
				@elseif(View::exists('orders.inputs.'.$input->attribute_name))
					@include('orders.inputs.'.$input->attribute_name,[
						'required' => $input->required,
						'size_class' => $size_class[$input->size],
						'form_lock' => $input->state == 2 && !in_array($input->attribute_name,$bsf_form_ignore) ? 'disabled' : $form_lock
					])
				@endif
			@endforeach
		</div>
	</div>
</div>
<div class="row">
	@foreach($input_list_down as $input)
		@if(strpos($input->attribute_name,"attribute_") !== false)
			@include('orders.inputs.attributes-template',['attribute_section' => 0,'only_attribute_id' => substr($input->attribute_name,10)])
		@elseif(View::exists('orders.inputs.'.$input->attribute_name))
			@include('orders.inputs.'.$input->attribute_name,[
				'required' => $input->required,
				'size_class' => $size_class[$input->size],
				'form_lock' => $input->state == 2 && !in_array($input->attribute_name,$bsf_form_ignore) ? 'disabled' : $form_lock
			])
		@endif
	@endforeach
</div>
