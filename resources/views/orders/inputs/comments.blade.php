@php
	if(isset($order->id) && !isset($duplikuj)){
		$order_last_message = \App\Models\OrderMessage::where('order_id',$order->id)->max('id');
	}
@endphp
<div class="{{$size_class}}">
	<div class="box box-widget" style="margin-bottom:10px">
		<div class="box-header with-border dts">
			<div class="user-block">
				<span class="username"><b>Komentarze do zlecenia</b></span>
			</div>
		</div>
		<div class="box-footer box-comments" style="{{isset($CommentHeight) ? 'max-height: '.$CommentHeight.'px !important;' : ''}}">
			<div id="order-box-comments-inner" data-last_message={{ $order_last_message ?? 0 }}>
				@if(isset($order->id) && !isset($duplikuj))
					@php $msg_controller = new \App\Http\Controllers\OrderMessageController(); @endphp
					{!! $msg_controller->getMessages($order->id) !!}
				@endif
			</div>
		</div>
		<div class="box-footer">
			<div class="img-push">
				<textarea {{ $uprawnieniaUzytkownika['access_Serwis_ZleceniaSerwisowe_Komentarz'] == 1 ? '' : 'disabled' }} class="textarea_auto_counter textarea_mention order-paste order-paste-textarea form-control hashtag_go_back" style="margin-bottom:0px;height:93px" name="wiadomosc" id="order_send_message" maxlength="2000" data-counter="order_comment_counter" placeholder="{!! isset($order->id) && !isset($duplikuj) ? 'Wciśnij enter aby wysłać komentarz' : 'Komentarz do nowego zlecenia' !!}"></textarea>
				<div style="display:flex">
					<p class="order_comment_counter comment_counter">Pozostało 2000 znaków do wykorzystania</p>
					<span class="comments_restore_container" onclick="Order_ReadCommentCache()" title="Przywróć komentarz" data-toggle="tooltip" data-container="body"><i class="fal fa-recycle mr-0"></i></span>
				</div>
				<div class="row blob-chat"></div>
				<input type="hidden" name="ObrazkiTEMP" id="order_new_message_images">
			</div>
		</div>
	</div>
</div>
