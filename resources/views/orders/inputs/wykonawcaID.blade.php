@php
	if(isset($order->wykonawcaID)){
		$order_selected_user_id = $order->wykonawcaID;
	} else if(old('wykonawcaID') != ''){
		$order_selected_user_id = old('wykonawcaID');
	} else {
		if($daneUzytkownikaAuth->IsInternalUser()){
			$order_selected_user_id = $daneUzytkownikaAuth->id;
		} else {
			$order_selected_user_id = null;
		}
	}
@endphp

@if($daneUzytkownikaAuth->IsInternalUser())
	<div class="{{$size_class}} input_height">
		<label for="wykonawcaID">Wykonawca</label>
		<select {{$form_lock}} {{$required == 1 ? 'required' : ''}} name="wykonawcaID" id="performerUserID" class="form-control sel2">
			@foreach($users as $user)
				@if($user->trashed())
					@if($order_selected_user_id == $user->id)
						<option value="{{$user->id}}" {{ $order_selected_user_id == $user->id ? 'selected' : '' }} data-performer_email="{!! $user->email !!}">{!! $user->name !!}</option>
					@endif
				@else
					<option value="{{$user->id}}" {{ $order_selected_user_id == $user->id ? 'selected' : '' }} data-performer_email="{!! $user->email !!}">{!! $user->name !!}</option>
				@endif
			@endforeach
		</select>
	</div>
@endif
