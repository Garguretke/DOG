@php $bsf_zalaczniki = \App\Models\BootstrapForm::select('hidden')->where('section_name','service_service_orders')->where('attribute_name','attachments')->first(); @endphp
<div class="modal-header">
	<h4 class="modal-title">
		<b>{!! isset($order->id) ? '<a href="'.(route('orders.edit',['id' => $order->id ?? 0])).'" class="message_uri">Zlecenie serwisowe</a>: <span class="hashtag" data-type="zlecenie">#'.$order->id.'</span>' : 'Nowe zlecenie serwisowe' !!}</b>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span>&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body" style="padding-top:0px;padding-bottom:10px">
	<div id="orderModalContent">
		<small>
			<ul class="nav nav-tabs settings_sections" id="orderTabs" role="tablist" style="margin-top: 0px;">
				<li class="nav-item">
					<a class="active" id="orderTabOgolne" data-toggle="tab" href="#OrderOgolne" role="tab" aria-controls="OrderOgolne" aria-selected="true">Ogólne</a>
				</li>
				@if(isset($order->id))
					<li class="nav-item">
						<a id="orderTabHistoria" data-toggle="tab" href="#OrderHistoria" role="tab" aria-controls="OrderHistoria" aria-selected="false">Historia</a>
					</li>
				@endif
				@if($bsf_zalaczniki->hidden != 1)
					<li class="nav-item orders_bsf_hidden">
						<a class="zoneDeleteAttachmentUpdate" id="orderTabZalaczniki" data-update="Zone_DeleteorderAttachment" data-toggle="tab" href="#OrderZalaczniki" role="tab" aria-controls="OrderZalaczniki" aria-selected="false">Załączniki (0)</a>
					</li>
				@endif
				@if($ustawieniaGlobalne->service_parts_and_operations != 0)
					@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
						<li class="nav-item orders_bsf_hidden">
							<a id="orderTabCzesci" data-toggle="tab" href="#OrderCzesci" role="tab" aria-controls="OrderCzesci" aria-selected="false">Części</a>
						</li>
					@endif
					@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
						<li class="nav-item orders_bsf_hidden">
							<a id="orderTabCzynnosci" data-toggle="tab" href="#OrderCzynnosci" role="tab" aria-controls="OrderCzynnosci" aria-selected="false">Czynności</a>
						</li>
					@endif
				@endif
			</ul>

			<div class="tab-content" id="orderTabContent">
				<div class="tab-pane active" id="OrderOgolne" role="tabpanel" aria-labelledby="OrderOgolne-tab" style="min-height: 630px">
					@include('orders.form-partial', ['with_attachments' => false, 'modal' => '#orderModal'])
				</div>

				<div class="tab-pane" id="OrderHistoria" role="tabpanel" aria-labelledby="OrderHistoria-tab" style="min-height: 630px">
					<div id="orderHistoryContent"></div>
				</div>

				@if($ustawieniaGlobalne->service_parts_and_operations != 0)
					@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
						<div class="tab-pane" id="OrderCzesci" role="tabpanel" aria-labelledby="OrderCzesci-tab" style="min-height: 630px">
							<div class="row">
								<div class="col-xs-12">
									<button id="addOrderServicePartButton" type="button" class="btn btn-success btn-xs pull-right">
										<i class="fal fa-plus-square mr-0"></i>
									</button>
								</div>
								<div class="col-xs-12">
									<div id="order_service_parts_content">
										@include('order-service-parts.content')
									</div>
								</div>
							</div>
						</div>
					@endif
					@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
						<div class="tab-pane" id="OrderCzynnosci" role="tabpanel" aria-labelledby="OrderCzynnosci-tab" style="min-height: 630px">
							<div class="row">
								<div class="col-xs-12">
									<button id="addOrderServiceOperationButton" type="button" class="btn btn-success btn-xs pull-right">
										<i class="fal fa-plus-square mr-0"></i>
									</button>
								</div>
								<div class="col-xs-12">
									<div id="order_service_operations_content">
										@include('order-service-operations.content')
									</div>
								</div>
							</div>
						</div>
					@endif
				@endif

				<div class="tab-pane" id="OrderZalaczniki" role="tabpanel" aria-labelledby="OrderZalaczniki-tab" style="min-height: 630px;max-height: 800px; overflow-y:auto; overflow-x: hidden">
					@include('attachments.zone',[
						'dz_model' => 'App\Models\OrdersFile',
						'dz_itemid' => isset($order->id) ? $order->id : 0,
						'dz_files' => isset($ordersFiles) ? $ordersFiles : [],
						'dz_item_name' => 'order',
						'dz_access_edit' => $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'],
						'dz_view_file_upload' => 'orders.file.upload',
						'dz_view_file_download' => 'orders.viewDownload',
						'dz_view_file_delete' => 'orders.file.remove'
					])
				</div>
			</div>
		</small>
	</div>
</div>

<div class="modal-footer">
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-9 col-xl-9 col-xxl-9 text-left">
			@if(
					($ModuleSettings->modul_crm && $ModuleSettings->modul_crm_zlecenia_handlowe && $uprawnieniaUzytkownika['access_CRM_ZleceniaHandlowe_Tworzenie'] == 1)
					||
					($ModuleSettings->modul_terminarz && $ModuleSettings->modul_terminarz_lista_wydarzen && $uprawnieniaUzytkownika['access_Terminarz_ListaWydarzen_Tworzenie'] == 1)
					||
					($ModuleSettings->modul_serwis && (($ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1) || ($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)))
					||
					($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Duplikacja'] == 1 && isset($order->id))
				)
				<div class="more_container orders_bsf_hidden">
					<div class="dropdown notification dropup">
						<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<button style="width:40px" class="btn btn-info disable-after-click" type="button" data-toggle="tooltip" title="Więcej">
								<i class="fal fa-plus-square mr-0"></i>
							</button>
						</div>
						<div class="dropdown-menu action offer_pdf_dropdown" style="width:255px">
							@if(isset($order->id))
								@if($ModuleSettings->modul_serwis)
									@if($ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1)
										<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTask" data-url="{{ route('tasks.create-from-order',['id' => $order->id]) }}">Przekształć w zadanie serwisowe</div>
									@endif
									@if($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)
										<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTaskCard" data-url="{{ route('task-cards.create-from-order',['id' => $order->id]) }}">Przekształć w protokół</div>
									@endif
								@endif
								@if($ModuleSettings->modul_crm && $ModuleSettings->modul_crm_zlecenia_handlowe && $uprawnieniaUzytkownika['access_CRM_ZleceniaHandlowe_Tworzenie'] == 1)
									<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTradeOrder" data-url="{{ route('trade-orders.create-from-order',['id' => $order->id]) }}">Przekształć w transakcje handlową</div>
								@endif
								@if($ModuleSettings->modul_terminarz && $ModuleSettings->modul_terminarz_lista_wydarzen && $uprawnieniaUzytkownika['access_Terminarz_ListaWydarzen_Tworzenie'] == 1)
									<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToEvent" data-url="{{ route('events.create-from-order',['id' => $order->id]) }}">Przekształć w wydarzenie</div>
								@endif
							@else
								@if($ModuleSettings->modul_serwis)
									@if($ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1)
										<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTask">Przekształć w zadanie serwisowe</div>
									@endif
									@if($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)
										<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTaskCard">Przekształć w protokół</div>
									@endif
								@endif
								@if($ModuleSettings->modul_crm && $ModuleSettings->modul_crm_zlecenia_handlowe && $uprawnieniaUzytkownika['access_CRM_ZleceniaHandlowe_Tworzenie'] == 1)
									<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToTradeOrder">Przekształć w transakcje handlową</div>
								@endif
								@if($ModuleSettings->modul_terminarz && $ModuleSettings->modul_terminarz_lista_wydarzen && $uprawnieniaUzytkownika['access_Terminarz_ListaWydarzen_Tworzenie'] == 1)
									<div style="color:#FFFFFF" class="offersPDFButton" id="convertOrderToEvent">Przekształć w wydarzenie</div>
								@endif
							@endif
							@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Duplikacja'] == 1)
								@if($form_lock == '')
									<div style="color:#FFFFFF" class="offersPDFButton" id="duplicateOrder">Duplikuj</div>
								@else
									<a href="{{ route('orders.duplicate',['id' => $order->id]) }}">
										<div style="color:#FFFFFF" class="offersPDFButton">Duplikuj</div>
									</a>
								@endif
							@endif
						</div>
					</div>
				</div>
			@endif
			@if(isset($order->id))
				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Zakanczanie'] == 1 && $order->finishData == '')
					<button type="button" id="orderFinishButton" title="Zakończ zlecenie" data-toggle="tooltip" class="btn btn-finish needs-confirmation" data-form-id="order_finish_form_modal" data-confirm-title="Wymagane potwierdzenie">
						<i class="fal fa-lock mr-0"></i>
					</button>
				@endif
			@endif
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3 text-right">
			<button type="button" class="btn btn-warning" title="Zamknij" data-toggle="tooltip" data-dismiss="modal">
				<i class="fal fa-times mr-0"></i>
			</button>
			@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'] == 1)
				<button type="button" title="Zapisz" data-toggle="tooltip" id="orderModalSaveButton" {!! isset($callback) ? 'data-callback="'.$callback.'"' : '' !!} class="btn btn-save orderSaveButton disable-after-click orders_bsf_hidden">
					<i class="fal fa-save mr-0"></i>
				</button>
			@endif
		</div>
	</div>
</div>
