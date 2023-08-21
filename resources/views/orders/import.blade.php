@extends('content-partial', ['title' => 'Importowanie zleceń serwisowych'])

@section('top-buttons')
	{!! ViewHelper::TopButtonBackList(route('orders')) !!}
	{!! ViewHelper::TopButtonSave('xlsImportForm','xlsImportFormSubmit',null,'Zaimportuj') !!}
@endsection

@section('content')
	@include('partials.import-xls')
@endsection
