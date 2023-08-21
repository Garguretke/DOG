@extends('content-partial', ['title' => (isset($duplikuj) ? 'Duplikuj zlecenie serwisowe: #'.$duplikuj : 'Nowe zlecenie serwisowe')])

@section('top-buttons')
	@if(isset($_GET['project_id']))
		{!! ViewHelper::TopButtonBack(route('eprojects.edit',['id' => $_GET['project_id']])) !!}
	@else
		{!! ViewHelper::TopButtonBackList(route('orders')) !!}
	@endif

	@php $GlobalFunction->forgetCacheSQL('PARAMS_ORDER_TO_PRODUCT'); @endphp

	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Tworzenie'] == 1)
		{!! ViewHelper::TopButtonSave('orderSaveForm','orderSaveButton orders_bsf_hidden') !!}
	@endif

	@if(
			($ModuleSettings->modul_crm && $ModuleSettings->modul_crm_zlecenia_handlowe && $uprawnieniaUzytkownika['access_CRM_ZleceniaHandlowe_Tworzenie'] == 1)
			||
			($ModuleSettings->modul_terminarz && $ModuleSettings->modul_terminarz_lista_wydarzen && $uprawnieniaUzytkownika['access_Terminarz_ListaWydarzen_Tworzenie'] == 1)
			||
			($ModuleSettings->modul_serwis && (($ModuleSettings->modul_serwis_zadania_serwisowe && $uprawnieniaUzytkownika['access_Serwis_ZadaniaSerwisowe_Tworzenie'] == 1) || ($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)))
		)
		<div style="float:left;padding-left:5px" class="orders_bsf_hidden">
			<div class="dropdown notification">
				<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<button style="width:40px" class="btn btn-info disable-after-click" type="button" data-toggle="tooltip" title="Więcej">
						<i class="fal fa-plus-square mr-0"></i>
					</button>
				</div>
				<div class="dropdown-menu action offer_pdf_dropdown" style="width:255px">
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
				</div>
			</div>
		</div>
	@endif
@endsection

@section('content')
	<div class="row esz_row">
		@include('right-tabs.form-partial', ['page' => 'orders', 'leftForm' => 'orders.form-left'])
	</div>
	<script>
		$(function(){
			$('#orderContactName').trigger('change');
		});
	</script>
@endsection
