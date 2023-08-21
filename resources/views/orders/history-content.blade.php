@php
	$attributes = \App\Services\HistoryHelper::ProcessDictionaryAttributes($attributes);
	$attribute_access = [];
	foreach($userAttributesAccessRead as $attributeid) $attribute_access[$attributeid] = true;
@endphp
<div class="card-body">
	<div class="row margin-10">
		<div class="col-xs-12">
			<ul class="timeline">
				@php
					foreach($history as $i => $hist){
						$extr_conf = json_decode($hist->conf,true);
						$flags[$i] = \App\Services\HistoryHelper::RenderTitle($extr_conf);
						$hist_title[$i] = $flags[$i]['title'];
						if(isset($extr_conf['has_file_added']) || isset($extr_conf['has_file_delete']) || isset($extr_conf['has_file_downloaded'])){
							$valks[$i]['attachment_name']['name'] = 'Nazwa pliku';
							$valks[$i]['attachment_name']['value'] = $extr_conf['attachment_name'] ?? 'Nieznana';
						}
						foreach($fields as $key => $value){
							if(isset($extr_conf['fields']) && array_key_exists($key,$extr_conf['fields'])){
								$valks[$i]['a_'.$key]['name'] = $value;
								switch($key){
									case 'mail_status': {
										$valks[$i]['a_'.$key]['value'] = ($extr_conf['fields'][$key] == 'SENT' || $extr_conf['fields'][$key] == '<i class="fal fa-envelope green"></i> Wysłana') ? 'Wysłane' : 'Niewysłane';
										break;
									}
									case 'finish_by':
									case 'wykonawcaID': {
										$valks[$i]['a_'.$key]['value'] = isset($users_data[$extr_conf['fields'][$key]]['name']) ? $users_data[$extr_conf['fields'][$key]]['name'] : 'Brak';
										break;
									}
									case 'tool_id': {
										$element = \App\Models\Tool::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? $element->name : 'Brak';
										break;
									}
									case 'dzialID': {
										$element = \App\Models\DivisionTypes::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? $element->name : 'Brak';
										break;
									}
									case 'statusID': {
										$element = \App\Models\StatusTypes::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? $element->name : 'Brak';
										break;
									}
									case 'bsf_type_id': {
										$element = \App\Models\OrderBsfType::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? $element->name : 'Brak';
										break;
									}
									case 'klientID':
									case 'odbiorcaID': {
										$element = \App\Models\Customer::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? $element->name : 'Brak kontrahenta';
										break;
									}
									case 'created_by_company': {
										$valks[$i]['a_'.$key]['value'] = $extr_conf['fields'][$key] ? 'NIE' : 'TAK';
										break;
									}
									case 'category_id': {
										$element = \App\Models\OrderCategory::withTrashed()->where('id',$extr_conf['fields'][$key])->first();
										$valks[$i]['a_'.$key]['value'] = !is_null($element) ? "[$element->code] ".$element->name : 'Brak';
										break;
									}
									case 'finishData':
									case 'planowanaDataRozpoczecia':
									case 'planowanaDataZakonczenia': {
										if(is_null($extr_conf['fields'][$key])){
											$valks[$i]['a_'.$key]['value'] = '-';
										} else {
											$valks[$i]['a_'.$key]['value'] = \Carbon\Carbon::parse($extr_conf['fields'][$key])->format("d.m.Y");
										}
										break;
									}
									default: {
										$valks[$i]['a_'.$key]['value'] = $extr_conf['fields'][$key];
										break;
									}
								}
							}
						}
						$valks[$i] = array_merge($valks[$i] ?? [],\App\Services\HistoryHelper::ProcessHistoryAttributes($extr_conf,$attributes,$attribute_access));
					}
				@endphp

				@foreach($history as $i => $hist)
					<li class="item {{$i % 2 == 0 ? '' : 'item timeline-inverted'}}">
						{!! \App\Services\HistoryHelper::RenderIcon($flags[$i],$loop) !!}
						<div class="timeline-panel">
							<div class="timeline-heading">
								<h4 class="timeline-title">
									<p class="history_head">{{ ($hist->id == ($history_first->id ?? 0) && $hist->created_by == $obj->created_by) ? 'Stworzenie' : (isset($hist_title[$i]) ? $hist_title[$i] : 'Zmiana') }}: <i class="fal fa-calendar-alt"></i> {{\Carbon\Carbon::parse($hist->created_at)->format('d.m.Y H:i:s')}}<br></p>
								</h4>
								<i class="fal fa-user"></i> <b>Dokonał: </b> <canvas width="10" height="10" style="margin-left:3px;margin-right:3px;background-color:{{ $users_data[$hist->updated_by]['color'] ?? '#000000' }}"></canvas>{{ $users_data[$hist->updated_by]['name'] ?? 'SYSTEM' }}
							</div>
							@if(isset($flags[$i]['has_element_attach']) || isset($flags[$i]['has_element_detach']))
								<b>{{ $flags[$i]['text'] }}</b>
								{!! \App\Services\HistoryHelper::RenderTimeline($i,$valks,$obj,$hist,$history_first,true,$flags) !!}
							@else
								{!! \App\Services\HistoryHelper::RenderTimeline($i,$valks,$obj,$hist,$history_first,true,$flags) !!}
							@endif
						</div>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
</div>
