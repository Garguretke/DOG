@extends('content-partial', ['title' => 'Zlecenia serwisowe'])

@section('top-buttons')
	@php
		if(isset($_GET['project_id'])){
			$GlobalFunction->setCacheSQL('PARAMS_PROJECT_TO_ORDER',json_encode($_GET));
		} else {
			if(empty($_GET)) $_GET = json_decode($GlobalFunction->getCacheSQL('PARAMS_PROJECT_TO_ORDER','[]'),true);
		}
	@endphp
	@if(isset($_GET['project_id']))
		{!! ViewHelper::TopButtonBack(route('eprojects.edit',['id' => $_GET['project_id']]),'Cofnij') !!}
		{!! ViewHelper::clearURI() !!}
	@endif

	@if(isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'query')
		@php $ready_to_create = false; @endphp
	@endif

	@if($ready_to_create)
		@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Tworzenie'] == 1)
			{!! ViewHelper::TopButtonAdd(route('orders.create'), 'Nowe zlecenie serwisowe') !!}
		@endif
	@endif

	<div style="float:left;padding-left:5px;">
		<div class="dropdown notification">
			<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<button style="width:40px" class="btn btn-primary disable-after-click" type="button" data-toggle="tooltip" title="Więcej">
					<i id="bootstrap_import_export" class="fal fa-sort-alt mr-0"></i>
				</button>
			</div>
			<div class="dropdown-menu action offer_pdf_dropdown" style="width:245px">
				@foreach($export_xls_template as $template)
					<a href="{{ route('orders.get-xls-bootstrap', ['id' => $template->id]) }}">
						<div style="color:#FFFFFF" class="offersPDFButton">Pobierz XLS: {{ $template->name }}</div>
					</a>
				@endforeach
				<div style="color:#FFFFFF" class="offersPDFButton" id="bootstrap_table_export_xls">Pobierz XLS: Widoczna tabela</div>
				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Import'] == 1)
					<a href="{{ route('orders.import') }}">
						<div style="color:#FFFFFF" class="offersPDFButton">Wgraj XLS</div>
					</a>
				@endif
			</div>
		</div>
	</div>

	{!! BootstrapRender::Buttons($config_section, $_GET ?? []) !!}
	<div class="multi_search_filter col-xs-3 col-sm-3 col-md-3 col-lg-2 col-xl-2 col-xxl-2">
		<input type="text" placeholder="Przeszukiwanie treści" name="multi_search" class="form-control text-center advanced-filter-input">
	</div>
@endsection

@section('content')
	<input type="text" name="customer" class="advanced-filter-input" style="display:none;">

	<div class="row esz_row">
		{!! BootstrapRender::Table($table_config,$config_section) !!}
	</div>

	@php
		if(isset($_GET['project_id']) && isset($_GET['action'])) $bootstrap_extra_data = "{'project_id' : ".$_GET['project_id'].",'action' : '".$_GET['action']."'}";
	@endphp

	@if($group_operation == true && (isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'add'))
		<div class="row esz_row" id="bs_group_operation_container">
			<div class="col-xs-12">
				@include('partials.collapsible-card', [
					'title' => 'Operacje grupowe',
					'content' => 'orders.group-operation',
					'save' => true,
					'cacheKey' => 'orders_group_operation',
					'card' => true
				])
			</div>
		</div>
	@endif

	<input type="hidden" id="customer_select_klientID" value="0">
	<input type="hidden" id="orderID" value="0">
	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1 || $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
		@include('orders.scripts')
	@endif
	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
		@include('order-service-operations.scripts')
		@include('order-service-operations.modal')
	@endif
	@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
		@include('order-service-parts.scripts')
		@include('order-service-parts.modal')
	@endif

	<script>
		function Bootstrap_GetFormatters(){

			DataBst.formatter.eye_formatter_ex = function(value, row, index, field){
				var table = [];
				table.push('<div style="display:inline-flex;float:left;">');
				if(row.deleted_at != null) table.push(bootstrap_delete_icon(row.deleted_at));
				if(row.performer_deleted != null) table.push(bootstrap_deleted_user_icon());
				if(row.event_data != null){
					var event_data = row.event_data.split('|'), date_start = new Date(event_data[1]), date_end = new Date(event_data[2]);
					if(date_start.getDay() == date_end.getDay() && date_start.getMonth() == date_end.getMonth() && date_start.getFullYear() == date_end.getFullYear()){
						table.push('<span class="badge symbol-badge" style="background-color:#00AA00;margin-left:5px" data-placement="right" data-toggle="tooltip" title="#'+event_data[0]+' '+bootstrap_format_date(event_data[1])+' '+bootstrap_format_time_ex(event_data[1])+' - '+bootstrap_format_time_ex(event_data[2])+'"><i class="fal fa-calendar-alt"></i></span>');
					} else {
						table.push('<span class="badge symbol-badge" style="background-color:#00AA00;margin-left:5px" data-placement="right" data-toggle="tooltip" title="#'+event_data[0]+' '+bootstrap_format_datetime_ex(event_data[1])+' - '+bootstrap_format_datetime_ex(event_data[2])+'"><i class="fal fa-calendar-alt"></i></span>');
					}
				}
				if(row.task_id > 0){
					table.push('<span class="badge symbol-badge" style="background-color:#00AA00;margin-left:5px" data-placement="right" data-toggle="tooltip" title="Zadanie serwisowe #'+row.task_id+'"><i class="fal fa-book"></i></span>');
				}
				if(row.trade_order_id > 0){
					table.push('<span class="badge symbol-badge" style="background-color:#00AA00;margin-left:5px" data-placement="right" data-toggle="tooltip" title="Transakcja handlowa #'+row.trade_order_id+'"><i class="fal fa-hands-helping" style="position:relative;left:-1.5px"></i></span>');
				}
				if(row.email_id > 0){
					table.push('<span class="badge symbol-badge" style="background-color:#00AA00;margin-left:5px" data-placement="right" data-toggle="tooltip" title="Wiadomość e-mail #'+row.email_id+'"><i class="fal fa-envelope mr-0"></i></span>');
				}
				if(row.message_count > 0) table.push(bootstrap_message_icon(row.message_count));
				if(row.attachment_count > 0) table.push(bootstrap_attachment_icon(row.attachment_count));
				if(row.finishData != null){
					table.push('<span class="badge symbol-badge" title="Zamknięte" data-toggle="tooltip" style="margin-left:5px"><i class="fal fa-lock"></i></span>');
				} else {
					table.push('<span class="badge symbol-badge" title="Otwarte" data-toggle="tooltip" style="background-color:#51D27D;margin-left:5px"><i class="fal fa-unlock"></i></span>');
				}
				table.push('</div>');
				return table.join('');
			}

			DataBst.formatter.id_formatter = function(value, row, index, field){
				if(row.opis != null && row.opis.length > 0){
					return `<span data-toggle="tooltip" title="`+escapeHtml(row.opis)+`">`+row.id+'</span>';
				} else {
					return '<span data-toggle="tooltip" title="Brak opisu">'+row.id+'</span>';
				}
			}

			DataBst.formatter.is_finished_formatter = function(value, row, index, field){
				return (row.finish_by == null ? 'NIE' : 'TAK');
			}

			DataBst.formatter.division_name = function(value, row, index, field){
				if(row.division_name == null) return '-';
				return row.division_name;
			}

			DataBst.formatter.terminOd = function(value, row, index, field){
				return bootstrap_format_date(row.planowanaDataRozpoczecia);
			}

			DataBst.formatter.terminDo = function(value, row, index, field){
				return bootstrap_format_date(row.planowanaDataZakonczenia);
			}

			DataBst.formatter.status_name = function(value, row, index, field){
				return bootstrap_status_formatter(row.status_name, row.status_icon);
			}

			DataBst.formatter.customer_code = function(value, row, index, field){
				if(row.cus_code == null) return '-';
				return escapeHtml(row.cus_code);
			}

			DataBst.formatter.customer_name = function(value, row, index, field){
				if(row.cus_name == null) return '-';
				return escapeHtml(row.cus_name);
			}

			DataBst.formatter.customer_address = function(value, row, index, field){
				if(row.customer_address == '' || row.customer_address == null) return '-';
				return escapeHtml(row.customer_address);
			}

			DataBst.formatter.recipient_code = function(value, row, index, field){
				return escapeHtml(row.rec_code);
			}

			DataBst.formatter.recipient_name = function(value, row, index, field){
				return escapeHtml(row.rec_name);
			}

			DataBst.formatter.godzinPrzepracowanych = function(value, row, index, field){
				if(row.godzinPrzepracowanych == null) return "0,00";
				return parseFloat(row.godzinPrzepracowanych).toFixed(2).replace(".",",");
			}

			DataBst.formatter.liczbaPlanowanychGodzin = function(value, row, index, field){
				return parseFloat(row.liczbaPlanowanychGodzin).toFixed(2).replace(".",",");
			}

			DataBst.formatter.liczbaZafakturowanychGodzin = function(value, row, index, field){
				return parseFloat(row.liczbaZafakturowanychGodzin).toFixed(2).replace(".",",");
			}

			DataBst.formatter.performer_group = function(value, row, index, field){
				return row.performer_group;
			}

			DataBst.formatter.tool_code = function(value, row, index, field){
				if(row.tool_code == null) return '-';
				return escapeHtml(row.tool_code);
			}

			DataBst.formatter.tool_name = function(value, row, index, field){
				if(row.tool_name == null) return '-';
				return escapeHtml(row.tool_name);
			}

			DataBst.formatter.tool_address = function(value, row, index, field){
				if(row.tool_address == null) return '-';
				return row.tool_address;
			}

			DataBst.formatter.bsf_type_name = function(value, row, index, field){
				if(row.bsf_type_id == null) return 'Standardowy';
				return select_option_formater('bsf_type_name', row.bsf_type_id);
			}

			DataBst.formatter.action_column = function(value, row, index, field){
				var table = [];
				table.push(Bootstrap_GetButtonData());
				@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'] == 1)
					if(row.finishData == null){
						table.push('<div class="bootstraptable_clicker" data-id="'+row.id+'">'+Bootstrap_RenderButtonEdit(row.id, 'Edytuj')+'</div>');
					} else {
						table.push('<div class="bootstraptable_clicker" data-id="'+row.id+'">'+Bootstrap_RenderButtonView(row.id, 'Podgląd')+'</div>');
					}
				@else
					table.push('<div class="bootstraptable_clicker" data-id="'+row.id+'">'+Bootstrap_RenderButtonView(row.id, 'Podgląd')+'</div>');
				@endif

				var dropdown = `<div style="padding-right:5px">
					<div class="dropdown notification">
						<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<button style="width:40px" class="btn btn-sm btn-info" type="button" data-toggle="tooltip" title="Pobierz PDF">
								<i class="fal fa-print mr-0"></i>
							</button>
						</div>
						<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">`;
							@foreach($pdf_templates as $template)
								dropdown += '<a download href="'+DataBst.request_url+'/'+row.id+'/pdf-download?template_id={{ $template->id }}" style="text-decoration:none;color:#FFFFFF"><div class="offersPDFButton">{{ addslashes($template->name) }}</div></a>';
							@endforeach
				dropdown += `
						</div>
					</div>
				</div>
				`;

				table.push(dropdown);

				var dropdown = `<div style="padding-right:5px">
					<div class="dropdown notification">
						<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<button style="width:40px" class="btn btn-sm btn-info" type="button" data-toggle="tooltip" title="Wyślij PDF">
								<i class="fal fa-paper-plane mr-0"></i>
							</button>
						</div>
						<div class="dropdown-menu action offer_pdf_dropdown" style="width:200px">`;
							@foreach($pdf_templates as $template)
								dropdown += '<a href="'+DataBst.request_url+'/'+row.id+'/send-mail?template_id={{ $template->id }}" style="text-decoration:none;color:#FFFFFF"><div class="offersPDFButton">{{ addslashes($template->name) }}</div></a>';
							@endforeach
				dropdown += `
						</div>
					</div>
				</div>
				`;

				table.push(dropdown);

				@if($ModuleSettings->modul_serwis)
					@if($ModuleSettings->modul_serwis_protokoly && $uprawnieniaUzytkownika['access_Serwis_Protokoly_Odczyt'] == 1)
						if(row.finishData === null){
							@if($uprawnieniaUzytkownika['access_Serwis_Protokoly_Tworzenie'] == 1)
								table.push(Bootstrap_RenderButton('{{route('task-cards.create')}}?trigger_set_order_id='+row.id, 'btn-success', 'fal fa-plus', 'Dodaj protokół'));
							@endif
						}
					@endif
					@if($ModuleSettings->modul_serwis_czesciiczynnosci && $ustawieniaGlobalne->service_parts_and_operations != 0)
						if(row.finishData === null){
							@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
								table.push(Bootstrap_RenderButtonScript("Orders_ModalTable_Operations", row.id+';'+row.klientID, 'btn-success', 'fal fa-user-cog', ' Dodaj czynność'));
							@endif
							@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
								table.push(Bootstrap_RenderButtonScript("Orders_ModalTable_Parts", row.id+';'+row.klientID, 'btn-success', 'fal fa-box-full', ' Dodaj część'));
							@endif
						}
					@endif
				@endif
				table.push('</div>');
				return table.join('');
			}

		}

		$(function(){
			@if($group_operation == true && (isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'add'))
				$('#bs_multi_project_add_order').on('click', function(e){
					e.preventDefault();
					if(!$(this).hasClass('disabled')) Orders_GroupOperation();
				});
			@endif
		});

		function Bootstrap_OnGroupOperationAccepted(){
			$('#bs_multi_project_add_order').trigger('click');
		}

		var bs_select_once = false;

		function OnBootstrapTableClick(target_id){
			@if(isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'add')
				if(!bs_select_once){
					bs_select_once = true;
					window.location.href = "{{ route('eprojects.item-add',['id' => $_GET['project_id']]) }}?element_type=order&element_id="+target_id;
				}
				return false;
			@endif
			return true;
		}

		@if($group_operation == true && (isset($_GET['project_id']) && isset($_GET['action']) && $_GET['action'] == 'add'))
			function Orders_GroupOperation(){
				var zaznaczone = Bootstrap_GetSelectedItems();
				if(zaznaczone.length <= 0){
					Flash_Message('danger',"Aby wykonać operacje grupową musisz zaznaczyć pozycje.");
				} else {
					Bootstrap_StartGroupOperation(zaznaczone.length);
					for(x = 0; x < zaznaczone.length; x++) Orders_GroupOperationCommand(zaznaczone[x]);
					Bootstrap_CheckGroupOperationFinish("Pomyślnie dodano elementy o ID: ","Nie udało się dodać elementów o ID: ");
				}
			}

			function Orders_GroupOperationCommand(id){
				$.get('{{ route('eprojects.item-add',['id' => $_GET['project_id']]) }}',{element_type : 'order', element_id : id}).done(function(response){
					Bootstrap_AppendGroupOperationSuccess(id);
				}).fail(function(xhr, status, error){
					Bootstrap_AppendGroupOperationFail(id);
				});
			}

			function Boostrap_OnGroupOperationFinish(){
				window.location.href = '{{ route('eprojects.edit',['id' => $_GET['project_id']]) }}';
			}
		@endif

		function Orders_ModalTable_Parts(args){
			$("#orderID").val(args[0]);
			$("#customer_select_klientID").val(args[1]);
			OrderServiceParts_OpenModal(0,OrderServiceParts_BindModalSave);
		}

		function Orders_ModalTable_Operations(args){
			$("#orderID").val(args[0]);
			$("#customer_select_klientID").val(args[1]);
			OrderServiceOperations_OpenModal(0,OrderServiceOperations_BindModalSave);
		}

		function Bootstrap_ApplyRemoteFilters(){
			@if(isset($_GET['customer']))
				Bootstrap_SetRemoteFilter('customer','{{ $_GET['customer'] }}');
			@endif
		}

		$(function(){
			var import_errors = "{!! $GlobalFunction->getCacheSQL('LAST_ORDERS_IMPORT_ERROR','') !!}";
			if(import_errors != ''){
				showMessage('Informacja','<b>Wystąpiły błędy podczas importowania pliku XLS</b><br>'+import_errors);
			}
		});
		@php $GlobalFunction->forgetCacheSQL('LAST_ORDERS_IMPORT_ERROR'); @endphp
	</script>
	@include('bootstrap-table.main')
@endsection
