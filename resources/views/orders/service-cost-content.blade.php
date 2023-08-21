@php
	$total_parts_included_brutto = 0;
	$total_parts_included_netto = 0;
	$total_parts_notincluded_brutto = 0;
	$total_parts_notincluded_netto = 0;

	if(!isset($order_service_parts_id)) $order_service_parts_id = [];
	foreach(\App\Models\OrderServicePart::whereIn('id',$order_service_parts_id)->get() as $service_part){
		if($service_part->include_invoice == 1){
			$total_parts_included_brutto += $service_part->price_t_brutto;
			$total_parts_included_netto += $service_part->price_t_netto;
		} else {
			$total_parts_notincluded_brutto += $service_part->price_t_brutto;
			$total_parts_notincluded_netto += $service_part->price_t_netto;
		}
	}

	$total_operations_included_brutto = 0;
	$total_operations_included_netto = 0;
	$total_operations_notincluded_brutto = 0;
	$total_operations_notincluded_netto = 0;

	if(!isset($order_service_operations_id)) $order_service_operations_id = [];
	foreach(\App\Models\OrderServiceOperation::whereIn('id',$order_service_operations_id)->get() as $service_operation){
		if($service_operation->include_invoice == 1){
			$total_operations_included_brutto += $service_operation->price_t_brutto;
			$total_operations_included_netto += $service_operation->price_t_netto;
		} else {
			$total_operations_notincluded_brutto += $service_operation->price_t_brutto;
			$total_operations_notincluded_netto += $service_operation->price_t_netto;
		}
	}

	$total_netto = $total_parts_included_netto + $total_operations_included_netto + $total_parts_notincluded_netto + $total_operations_notincluded_netto;
	$total_brutto = $total_parts_included_brutto + $total_operations_included_brutto + $total_parts_notincluded_brutto + $total_operations_notincluded_brutto;
@endphp

<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Netto</th>
				<th>Brutto</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Czynności na FS/PA</td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_operations_included_netto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_operations_included_brutto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
			</tr>
			<tr>
				<td>Czynności pozostałe</td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_operations_notincluded_netto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_operations_notincluded_brutto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
			</tr>
			<tr>
				<td>Części na RW</td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_parts_notincluded_netto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_parts_notincluded_brutto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
			</tr>
			<tr>
				<td>Części na FS/PA</td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_parts_included_netto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_parts_included_brutto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
			</tr>
			<tr>
				<td>Razem</td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_netto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
				<td><input type="number" class="form-control offer_no_margin" value="{{ ViewHelper::OffersRound($total_brutto,$ustawieniaGlobalne->offer_precision) }}" readonly></td>
			</tr>
		</tbody>
	</table>
</div>
