@php
	if(isset($order->id)){
		$orderID = $order->id;
	} else {
		$orderID = $duplikuj ?? null;
	}
@endphp

@include('attributes.attributes-inputs', ['id' => $orderID, 'typeAtr' => 1, 'attribute_section' => $attribute_section, 'only_attribute_id' => $only_attribute_id ?? 0])

@include('attributes.attributes-check', ['id' => $orderID, 'typeAtr' => 1, 'attribute_section' => $attribute_section, 'only_attribute_id' => $only_attribute_id ?? 0])

@include('attributes.attributes-signature', ['id' => $orderID, 'typeAtr' => 1, 'attribute_section' => $attribute_section, 'only_attribute_id' => $only_attribute_id ?? 0])
