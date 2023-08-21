<div class="{{$size_class}}">
	<label for="uwagi">Uwagi</label>
	<textarea {{$form_lock}} {{$required == 1 ? 'required' : ''}} name="uwagi" id="orderCommentsInput" maxlength="2000" data-counter="order_remarks_counter" class="textarea_auto_counter form-control" rows="9" style="max-width:100%;min-width:100%;height:162px;margin-bottom:0px">{{isset($order->uwagi) ? $order->uwagi : old('uwagi')}}</textarea>
	<p class="order_remarks_counter description_counter">Pozostało 2000 znaków do wykorzystania</p>
</div>
