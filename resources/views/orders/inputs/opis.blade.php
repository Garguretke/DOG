<div class="{{$size_class}}">
	<label for="opis">Opis</label>
	<textarea {{$form_lock}} {{$required == 1 ? 'required' : ''}} name="opis" id="orderDescriptionInput" data-maxlength="2000" maxlength="65536" data-counter="order_description_counter" class="textarea_auto_counter form-control" rows="9" style="max-width:100%;min-width:100%;height:162px;margin-bottom:0px">{{isset($order->opis) ? $order->opis : old('opis')}}</textarea>
	<p class="order_description_counter description_counter">Pozostało 2000 znaków do wykorzystania</p>
</div>
