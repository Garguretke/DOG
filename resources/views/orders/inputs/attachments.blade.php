@if($with_attachments ?? false)
	<div class="{{$size_class}}" style="min-height: 330px; margin-top: 10px">
		@include('attachments.zone',[
			'dz_model' => 'App\Models\OrdersFile',
			'dz_itemid' => isset($order->id) ? $order->id : 0,
			'dz_files' => isset($ordersFiles) ? $ordersFiles : [],
			'dz_item_name' => 'order',
			'dz_access_edit' => $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Edycja'] == 1 && ((isset($order->id) && is_null($order->finish_by)) || !isset($order->id)),
			'dz_view_file_upload' => 'orders.file.upload',
			'dz_view_file_download' => 'orders.viewDownload',
			'dz_view_file_delete' => 'orders.file.remove'
		])
	</div>
@endif
