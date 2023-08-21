@extends('content-partial', ['title' => $action_name.' zlecenia serwisowego: <span class="hashtag" data-type="zlecenie">#'.$order->id.'</span> '.(!is_null($order->nrZleceniaOptima) ? ', Optima: '.$order->nrZleceniaOptima : '')])

@section('top-buttons')
	@if(isset($_GET['project_id']))
		{!! ViewHelper::TopButtonBack(route('eprojects.edit',['id' => $_GET['project_id']])) !!}
	@else
		{!! ViewHelper::TopButtonBackList(route('orders')) !!}
	@endif
	{!! ViewHelper::TopButtonHistory(route('orders.history', ['id' => $order->id])) !!}

	@php $GlobalFunction->forgetCacheSQL('PARAMS_ORDER_TO_PRODUCT'); @endphp

	@if($form_lock == '' && $order->finishData == '')
		@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'] == 1)
			{!! ViewHelper::TopButtonSave('orderSaveForm','orderSaveButton') !!}
		@endif
	@endif

	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Zakanczanie'] == 1)
		@if($order->finishData == '')
			<button type="button" title="Zakończ zlecenie" data-toggle="tooltip" class="btn btn-finish needs-confirmation" style="min-width:40px" data-form-id="finish_form_{{$order->id}}"
				data-confirm-title="Wymagane potwierdzenie" data-confirm-description="Potwierdź zakończenie zlecenia serwisowego {{$order->name}}">
				<i class="fal fa-lock mr-0"></i>
			</button>
		@endif
	@endif

	@if(
			($ModuleSettings->modul_crm && $ModuleSettings->modul_crm_zlecenia_handlowe && $uprawnieniaUzytkownika['access_CRM_ZleceniaHandlowe_Tworzenie'] == 1)
			||
			($ModuleSettings->modul_terminarz && $ModuleSettings->modul_terminarz_lista_wydarzen && $uprawnieniaUzytkownika['access_Terminarz_ListaWydarzen_Tworzenie'] == 1)
			||
			$uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Duplikacja'] == 1
			||
			($ModuleSettings->modul_serwis && (($ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1) || ($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)))
		)
		<div style="float:left;padding-left:5px;">
			<div class="dropdown notification">
				<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<button style="width:40px" class="btn btn-info disable-after-click" type="button" data-toggle="tooltip" title="Więcej">
						<i class="fal fa-plus-square mr-0"></i>
					</button>
				</div>
				<div class="dropdown-menu action offer_pdf_dropdown" style="width:255px">
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

	@if($pdf_templates->count() > 0)
		@if($form_lock == '')
			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Podgląd PDF">
							<i class="fal fa-eye mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<div data-type="preview" data-template="{{ $template->id }}" style="color:#FFFFFF" class="ordersPDF offersPDFButton">{{ $template->name }}</div>
						@endforeach
					</div>
				</div>
			</div>

			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Pobierz PDF">
							<i class="fal fa-print mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<div data-type="download" data-template="{{ $template->id }}" style="color:#FFFFFF" class="ordersPDF offersPDFButton">{{ $template->name }}</div>
						@endforeach
					</div>
				</div>
			</div>

			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Wyślij PDF">
							<i class="fal fa-paper-plane mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<div data-type="send-email" data-template="{{ $template->id }}" style="color:#FFFFFF" class="ordersPDF offersPDFButton">{{ $template->name }}</div>
						@endforeach
					</div>
				</div>
			</div>
		@else
			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Podgląd PDF">
							<i class="fal fa-eye mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<a href="{{ route('orders.get-pdf-preview',['id' => $order->id]) }}?template_id={{ $template->id }}" style="text-decoration:none;color:#FFFFFF">
								<div class="offersPDFButton">{{ $template->name }}</div>
							</a>
						@endforeach
					</div>
				</div>
			</div>

			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Pobierz PDF">
							<i class="fal fa-print mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<a download href="{{ route('orders.get-pdf-download',['id' => $order->id]) }}?template_id={{ $template->id }}" style="text-decoration:none;color:#FFFFFF">
								<div class="offersPDFButton">{{ $template->name }}</div>
							</a>
						@endforeach
					</div>
				</div>
			</div>

			<div style="float:left;padding-left:5px;">
				<div class="dropdown notification">
					<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<button style="width:40px" class="btn btn-info" type="button" data-toggle="tooltip" title="Wyślij PDF">
							<i class="fal fa-paper-plane mr-0"></i>
						</button>
					</div>
					<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">
						@foreach($pdf_templates as $template)
							<a href="{{ route('orders.send-mail',['id' => $order->id]) }}?template_id={{ $template->id }}" style="text-decoration:none;color:#FFFFFF">
								<div class="offersPDFButton">{{ $template->name }}</div>
							</a>
						@endforeach
					</div>
				</div>
			</div>
		@endif
	@else
		<button style="width:40px" class="disabled btn btn-info" type="button" data-toggle="tooltip" title="Brak szablonu wydruku">
			<i class="fal fa-eye mr-0"></i>
		</button>
		<button style="width:40px" class="disabled btn btn-info" type="button" data-toggle="tooltip" title="Brak szablonu wydruku">
			<i class="fal fa-print mr-0"></i>
		</button>
		<button style="width:40px" class="disabled btn btn-info" type="button" data-toggle="tooltip" title="Brak szablonu wydruku">
			<i class="fal fa-paper-plane mr-0"></i>
		</button>
	@endif
@endsection

@section('content')
	<div class="row esz_row">
		@include('right-tabs.form-partial', ['page' => 'orders', 'leftForm' => 'orders.form-left'])
	</div>
@endsection
