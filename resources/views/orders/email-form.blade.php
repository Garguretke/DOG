@extends('content-partial', ['title' => 'Wysyłka zlecenia'])

@section('top-buttons')
	{!! ViewHelper::TopButtonBack(route('orders.edit',['id' => $order->id])) !!}
	{!! ViewHelper::TopButtonHistory(route('orders.history', ['id' => $order->id])) !!}
	{!! ViewHelper::TopButtonEx('btn-save disable-after-click', 'fal fa-envelope', 'Wyślij', null, 'submit', 'orderEmailForm') !!}
@endsection

@section('content')
	@php
		$template_id = old('template_id') ?? $_GET['template_id'] ?? $pdf_default_template->id ?? 0;
		$email_id = old('email_id') ?? $_GET['email_id'] ?? $email_default_template->id ?? 0;
	@endphp
	<div class="row esz_row input_fix_container">
		<div class="col-xs-12">
			<div class="card">
				<div class="card-body">
					<form id="orderEmailForm" method="POST" enctype="multipart/form-data" class="submit-form">
						@csrf
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-8">
								<label for="emails[]">Adresy e-mail</label>
								<select id="emails" name="emails[]" multiple class="sel2 form-control {{$errors->has('emails') ? 'is-invalid' : ''}}" data-tags="true" style="height:32px" required>
									@foreach($customer_emails as $email)
										<option value="{{$email}}">{{$email}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-3 col-xxl-2">
								<label for="email_id">Szablon e-mail</label>
								<select name="email_id" class="form-control sel2" required>
									<option></option>
									@foreach($email_templates as $template)
										<option value="{{$template->id}}" {{ $email_id == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-3 col-xxl-2">
								<label for="template_id">Szablon wydruku</label>
								<select name="template_id" class="form-control sel2" required>
									<option></option>
									@foreach($pdf_templates as $template)
										<option value="{{$template->id}}" {{ $template_id == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12" style="min-height:330px">
								<label for="attachments[]">Załączniki (wydruk zlecenia zostanie dołączony automatycznie)</label><br>
								<input id="attachments" name="attachments[]" type="file" multiple="multiple">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	@include('layouts.editor-js-partial')

	<script>
		$(function(){

			$("#attachments").fileinput({
				'showUpload' : false,
				'previewFileType' : 'any',
				'multiple' : true,
				'language' : 'pl',
				'append' : true,
				'autoReplace' : false,
				'maxFileCount' : 5
			});

		});
	</script>
@endsection
