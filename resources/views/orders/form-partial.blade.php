@php
	$types = \App\Models\OrderBsfType::whereIn('id',$userBsfTypesAccess['order'])->get();
	$only_one = ($types->count() == 1);
	if($ustawieniaGlobalne->bsf_types_orders == 1){
		if($only_one){
			$bsf_type_id = $types->first()->id;
		} else if($daneUzytkownikaAuth->default_bsf_type_order != 0){
			$bsf_type_id = $types->where('id',$daneUzytkownikaAuth->default_bsf_type_order)->first()->id ?? null;
		}
	}
@endphp
<div id="orders_bsf_container">
	@if(isset($order->id) || isset($_GET['trigger_load_buffer']) || $ustawieniaGlobalne->bsf_types_orders == 0 || isset($bsf_type_id))
		@include('orders.form-partial-content')
	@else
		<style>
			.orders_bsf_hidden {
				display:none !important;
			}
		</style>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 col-xxl-4 input_height">
				<label>Rodzaj</label>
				<select id="order_bsf_type_id" class="form-control sel2">
					<option></option>
					@foreach($types as $type)
						<option value="{{$type->id}}">{{ $type->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<input type="hidden" id="order_create_from_tradeorder" value="{{$create_from_tradeorder ?? 0}}">
		<input type="hidden" id="order_create_from_task" value="{{$create_from_task ?? 0}}">
		<input type="hidden" id="order_create_from_event" value="{{$create_from_event ?? 0}}">
		<input type="hidden" id="order_dz_attachment_to_save" value="{{$dz_attachment_to_save ?? ''}}">
	@endif
</div>

@if(isset($duplikuj) && !isset($_GET['trigger_load_buffer']))
	@if($ModuleSettings->modul_serwis_czesciiczynnosci && $ustawieniaGlobalne->service_parts_and_operations != 0 && ($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1 || $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1))
		<div class="modal" id="orderextraModal" data-hidescroll="true" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">
							<b>Dodatkowe parametry duplikowania</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span>&times;</span>
							</button>
						</h4>
					</div>
					<div class="modal-body" style="padding-top:0px;padding-bottom:10px">
						<small>
							<br>
							<div class="row">
								@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czesci'] == 1)
									<div class="col-xs-12">
										<input type="checkbox" class="form-check-input fix_dla_checka" id="order_clone_parts" name="order_clone_parts">
										<label class="form-check-label" for="order_clone_parts">Skopiuj części serwisowe</label>
									</div>
								@endif
								@if($uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Czynnosci'] == 1)
									<div class="col-xs-12">
										<input type="checkbox" class="form-check-input fix_dla_checka" id="order_clone_operations" name="order_clone_operations">
										<label class="form-check-label" for="order_clone_operations">Skopiuj czynności serwisowe</label>
									</div>
								@endif
								<div class="col-xs-12">
									<input type="checkbox" class="form-check-input fix_dla_checka" id="order_clone_attachments" name="order_clone_attachments" checked>
									<label class="form-check-label" for="order_clone_attachments">Skopiuj załączniki</label>
								</div>
							</div>
						</small>
					</div>
					<div class="modal-footer">
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-9 col-xl-9 col-xxl-9 text-left">

							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3 text-right">
								<button type="button" title="OK" data-toggle="tooltip" class="btn btn-save" id="order_button_clone_extra">
									<i class="fal fa-check mr-0"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(function(){
				$('#orderextraModal').modal('show');
				$('#order_button_clone_extra').on('click',function(e){
					if(!$('#order_clone_attachments').is(':checked')){
						$('#orderItems').val('[]');
						$('#orderAttachmentsList').find('.slideObrazek').remove();
					}
					if($('#order_clone_parts').is(':checked') || $('#order_clone_operations').is(':checked')){
						var btn = $(this);
						if(btn.is(':disabled')) return;
						btn.prop('disabled',true);
						Flash_Message('success', 'Kopiowanie elementów w toku');
						$.post('{{ route('orders.clone-extra') }}',{
							'duplikuj' : '{{ $duplikuj }}',
							'clone_parts' : $('#order_clone_parts').is(':checked') ? 1 : 0,
							'clone_operations' : $('#order_clone_operations').is(':checked') ? 1 : 0,
						}).done(function(response){
							$("#OrderServicePartCollection").val(JSON.stringify(response.parts));
							OrderServicePartEMU.items_id = response.parts;
							OrderServiceParts_ReloadCards();
							$("#OrderServiceOperationCollection").val(JSON.stringify(response.operations));
							OrderServiceOperationEMU.items_id = response.operations;
							OrderServiceOperations_ReloadCards();
							$('#orderextraModal').modal('hide');
						}).fail(function(xhr, status, error){
							showAlert('Niepowodzenie', GetErrorMessage(xhr.status, 'Nie udało się wykonać operacji.'));
						}).always(function(){
							btn.prop('disabled',false);
						});
					} else {
						$('#orderextraModal').modal('hide');
					}
				});
			});
		</script>
	@endif
@endif

<div class="row">
	<div class="col-lg-12">
		@if($daneUzytkownikaAuth->show_creation && isset($order->id) && !isset($duplikuj))
			<table class="dts">
				<tr>
					<td style="min-width:100px"><strong>Stworzył: </strong></td>
					<td class="ets">{!! $order->getCreationInfo() !!}</td>
				</tr>
				<tr>
					<td><strong>Zmodyfikował: </strong></td>
					<td class="ets">{!! $order->getModificationInfo() !!}</td>
				</tr>
			</table>
		@endif
	</div>
</div>

@include('orders.styles')
