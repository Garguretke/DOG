@extends('layouts.app')

@section('title', 'MercjaLiqCalc')

@section('content')

<link href="{{ asset('source/css/liqcalc.css') }}" rel="stylesheet">

@if (app()->getLocale() === 'en')
	<script src="{{ asset('source/js/liqcalc_en.js') }}"></script>
@elseif (app()->getLocale() === 'pl')
	<script src="{{ asset('source/js/liqcalc_pl.js') }}"></script>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
				@if (app()->getLocale() === 'en')
					<div class="card-header">{{ __('Liquid strength calculator') }}</div>
				@elseif (app()->getLocale() === 'pl')
					<div class="card-header">{{ __('Kalkulator mocy liquidu') }}</div>
				@endif
				
                <div class="card-body mb-3">
					<form method="get" action="#">
						@if (app()->getLocale() === 'en')
							<h5>What do you want to count?</h5>
						@elseif (app()->getLocale() === 'pl')
							<h5>Co chcesz policzyć?</h5>
						@endif
							<select class="form-select form-select-sm mb-3" id="policz" name="policz" onchange="cLiquidCalc.changeMode(this.value);">
								@if (app()->getLocale() === 'en')
									<option value="0">(1) The resulting nicotine strength after mixing two liquids</option>
									<option value="1">(2) The resulting nicotine strength after mixing several liquids</option>
									<option value="2">(3) The amount of fluid needed to obtain the given strength</option>
									<option value="3">(4) The number of ingredients needed to obtain a specific amount of liquid with a given strength</option>
								@elseif (app()->getLocale() === 'pl')
									<option value="0">(1) Wynikową moc nikotyny po zmieszaniu dwóch liquidów</option>
									<option value="1">(2) Wynikową moc nikotyny po zmieszaniu kilku liquidów</option>
									<option value="2">(3) Ilość płynu potrzebnego do uzyskania zadanej mocy</option>
									<option value="3">(4) Ilość składników potrzebnych do uzyskania konkretnej ilości liquidu o zadanej mocy</option>
								@endif
							</select>
						<div id="forform0" class="mb-3">
							<fieldset>
								<div class="mb-3">
									@if (app()->getLocale() === 'en')
									<h5>Base liquid</h5>
									<label class="col-md-6" for="iloscs0">Amount of base liquid [ml]:</label>
									<input type="text" class="form-control w-25" name="iloscs0" id="iloscs0" />
									<label class="col-md-6" for="mocs0">Strength of the base liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc0s" id="mocs0" />
									@elseif (app()->getLocale() === 'pl')
									<h5>Liquid bazowy</h5>
									<label class="col-md-6" for="iloscs0">Ilość liquidu bazowego [ml]:</label>
									<input type="text" class="form-control w-25" name="iloscs0" id="iloscs0" />
									<label class="col-md-6" for="mocs0">Moc liquidu bazowego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc0s" id="mocs0" />
									@endif
								</div>
								<div class="mb-3">
									@if (app()->getLocale() === 'en')
										<h5>Dilution liquid</h5>
										<label class="col-md-6" for="iloscd0">Amount of dilution liquid [ml]:</label>
										<input type="text" class="form-control w-25" name="iloscd0" id="iloscd0" />
										<label class="col-md-6" for="mocd0">Strength of dilution liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="mocd0" id="mocd0" />
									@elseif (app()->getLocale() === 'pl')
										<h5>Liquid rozcieńczający</h5>
										<label class="col-md-6" for="iloscd0">Ilość liquidu rozcieńczającego [ml]:</label>
										<input type="text" class="form-control w-25" name="iloscd0" id="iloscd0" />
										<label class="col-md-6" for="mocd0">Moc liquidu rozcieńczającego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="mocd0" id="mocd0" />
									@endif
								</div>
							</fieldset>
						</div>
						<div id="forform1">
							<fieldset>
								@if (app()->getLocale() === 'en')
									<div class="mb-3">
										<h5>Liquids in the mixture</h5>
										<select name="liquidy" id="liquidy" class="form-select w-25" size="5" multiple="multiple">
										<option value="">---</option>
										</select>
									</div>
									<div class="mb-5">
										<input class="btn btn-primary btn-sm" type="button" value="Delete selected" title="Remove from mixture" onclick="cLiquidCalc.remSelected();"/>
										<input class="btn btn-primary btn-sm" type="button" value="Delete all" title="Remove all from the mixture" onclick="cLiquidCalc.remAll();"/>
									</div>
								@elseif (app()->getLocale() === 'pl')
									<div class="mb-3">
										<h5>Liquidy w mieszaninie</h5>
										<select name="liquidy" id="liquidy" class="form-select w-25" size="5" multiple="multiple">
										<option value="">---</option>
										</select>
									</div>
									<div class="mb-5">
										<input class="btn btn-primary btn-sm" type="button" value="Usuń wybrane" title="Usuń z mieszaniny" onclick="cLiquidCalc.remSelected();"/>
										<input class="btn btn-primary btn-sm" type="button" value="Usuń wszystkie" title="Usuń wszystkie z mieszaniny" onclick="cLiquidCalc.remAll();"/>
									</div>
								@endif
							</fieldset>	
							<fieldset>
								@if (app()->getLocale() === 'en')
									<div class="mb-3">
										<h5>Add liquid to the mixture</h5>
										<label class="col-md-6" for="ilosc">Liquid quantity [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc" id="ilosc" />
										<label class="col-md-6" for="moc">Strength of liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc" id="moc" />
									</div>
									<div class="mb-3">
										<input class="btn btn-primary btn-sm" type="button" value="Add" title="Add to the mixture" onclick="cLiquidCalc.addSel();"/>
									</div>
								@elseif (app()->getLocale() === 'pl')
									<div class="mb-3">
										<h5>Dodaj liquid do mieszaniny</h5>
										<label class="col-md-6" for="ilosc">Ilość liquidu [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc" id="ilosc" />
										<label class="col-md-6" for="moc">Moc liquidu [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc" id="moc" />
									</div>
									<div class="mb-3">
										<input class="btn btn-primary btn-sm" type="button" value="Dodaj" title="Dodaj do mieszaniny" onclick="cLiquidCalc.addSel();"/>
									</div>
								@endif
							</fieldset>			
						</div>
						<div id="forform2">
							<div class="mb-3">
								<fieldset>
									@if (app()->getLocale() === 'en')
										<h5>Base liquid</h5>
										<label class="col-md-6" for="ilosc1">Amount of base liquid [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc1" id="ilosc1" />
										<label class="col-md-6" for="moc1">Strength of the base liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc1" id="moc1" />
									@elseif (app()->getLocale() === 'pl')
										<h5>Liquid bazowy</h5>
										<label class="col-md-6" for="ilosc1">Ilość liquidu bazowego [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc1" id="ilosc1" />
										<label class="col-md-6" for="moc1">Moc liquidu bazowego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc1" id="moc1" />
									@endif
								</fieldset>
							</div>
							<div class="mb-3">
								<fieldset>
									@if (app()->getLocale() === 'en')
										<h5>Strength</h5>
										<label class="col-md-6" for="moc2">Strength expected [mg/ml]:</label>
										<input type="text" class="form-control w-25" name="moc2" id="moc2" />
										<label class="col-md-6" for="moc3">Strength of dilution liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc3" id="moc3" />
										<label class="col-md-6" for="procent">I want to add glycerin so that the resulting liquid has [%] of it: <span class="help help1 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="procent" id="procent" />
										<label class="col-md-6" for="alko1">I want to add concentrated ethyl alcohol <br />(e.g. rectified spirit) so that the resulting liquid contains [%] of it: <span class="help help2 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="alko1" id="alko1" />
										<label class="col-md-6" for="ilosc1liquid">Include flavor in the liquid [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc1liquid" id="ilosc1liquid" />
									@elseif (app()->getLocale() === 'pl')
										<h5>Moce</h5>
										<label class="col-md-6" for="moc2">Moc oczekiwana [mg/ml]:</label>
										<input type="text" class="form-control w-25" name="moc2" id="moc2" />
										<label class="col-md-6" for="moc3">Moc liquidu rozcieńczającego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="moc3" id="moc3" />
										<label class="col-md-6" for="procent">Chcę dodać glicerynę, by liquid wynikowy miał jej [%]: <span class="help help1 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="procent" id="procent" />
										<label class="col-md-6" for="alko1">Chcę dodać stężony alkohol etylowy <br />(np. spirytus rektyfikowany), by liquid wynikowy miał go [%]: <span class="help help2 btn btn-info btn-dark">?</span></label>
										<input type="text" class="form-control w-25" name="alko1" id="alko1" />
										<label class="col-md-6" for="ilosc1liquid">Uwzględnij aromat do liquidu [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc1liquid" id="ilosc1liquid" />
									@endif
								</fieldset>
							</div>
						</div>
						<div id="forform3">
							<div class="mb-3">
								<fieldset>
									@if (app()->getLocale() === 'en')
										<h5>I want to get</h5>
										<label class="col-md-6" for="ilosc4">Liquid quantity [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc4" id="ilosc4" />
										<label class="col-md-6" for="moc4">Strength of liquid [mg/ml]:</label>
										<input type="text" class="form-control w-25" name="moc4" id="moc4" />
									@elseif (app()->getLocale() === 'pl')
										<h5>Chcę uzyskać</h5>
										<label class="col-md-6" for="ilosc4">Ilość liquidu [ml]:</label>
										<input type="text" class="form-control w-25" name="ilosc4" id="ilosc4" />
										<label class="col-md-6" for="moc4">Moc liquidu [mg/ml]:</label>
										<input type="text" class="form-control w-25" name="moc4" id="moc4" />
									@endif
								</fieldset>
							</div>
							<div class="mb-3">
								<fieldset>
									@if (app()->getLocale() === 'en')
									<h5>I have it available</h5>
									<label class="col-md-6" for="moc6">Strength of the base liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc6" id="moc6" />
									<label class="col-md-6" for="moc7">Strength of dilution liquid [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc7" id="moc7" />
									<label class="col-md-6" for="procent2">I want to add glycerin so that the resulting liquid has [%] of it: <span class="help help1 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="procent2" id="procent2" />
									<label class="col-md-6" for="alko2">I want to add concentrated ethyl alcohol <br />(e.g. rectified spirit) so that the resulting liquid contains [%] of it: <span class="help help2 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="alko2" id="alko2" />
									<label class="col-md-6" for="ilosc2liquid">Include flavor in the liquid [ml]:</label>
									<input type="text" class="form-control w-25" name="ilosc2liquid" id="ilosc2liquid" />
									@elseif (app()->getLocale() === 'pl')
									<h5>Mam dostępne</h5>
									<label class="col-md-6" for="moc6">Moc liquidu bazowego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc6" id="moc6" />
									<label class="col-md-6" for="moc7">Moc liquidu rozcieńczającego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="moc7" id="moc7" />
									<label class="col-md-6" for="procent2">Chcę dodać glicerynę, by liquid wynikowy miał jej [%]: <span class="help help1 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="procent2" id="procent2" />
									<label class="col-md-6" for="alko2">Chcę dodać stężony alkohol etylowy <br />(np. spirytus rektyfikowany), by liquid wynikowy miał go [%]: <span class="help help2 btn btn-info btn-dark">?</span></label>
									<input type="text" class="form-control w-25" name="alko2" id="alko2" />
									<label class="col-md-6" for="ilosc2liquid">Uwzględnij aromat do liquidu [ml]:</label>
									<input type="text" class="form-control w-25" name="ilosc2liquid" id="ilosc2liquid" />
									@endif
								</fieldset>
							</div>
						</div>
						<div id="odpowiedz">
							@if (app()->getLocale() === 'en')
								<p>Answer for the given calculation will appear here...</p>
							@elseif (app()->getLocale() === 'pl')
								<p>Tu pojawi się odpowiedź dla zadanego obliczenia...</p>
							@endif
						</div>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	cLiquidCalc.prepare();
</script>
@endsection