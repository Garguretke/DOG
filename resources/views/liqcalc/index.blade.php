@extends('layouts.app')

@section('title', 'MercjaLiqCalc')

@section('content')

<link href="{{ asset('source/css/liqcalc.css') }}" rel="stylesheet">

<script type="text/javascript">
	var cLiquidCalc={
		helpStr : new Array(
          'Jeśli moc dotyczy samego PG, VG, aromatu beznikotynowego lub innej substancji nie zawierającej nikotyny, należy wpisać 0 lub pozostawić to pole puste',
          'Wpisana wartość dotyczy udziału procentowego gliceryny w wynikowym roztworze, nie zaś w dodawanych składnikach',
          'Należy zwrócić szczególną uwagę na ilość dodawanego alkoholu. <strong>Inhalacja oparów alkoholu w zbyt dużym stężeniu może powodować poparzenia dróg oddechowych, a także może powodować uszkodzenie inhalatora</strong>. Maksymalne stężenie apirytusu zostało ograniczone do 5% w roztworze'
          ),
		currentMode : 0,
		deftxt : '',
		maxAlco : 5,
		helper : null,
      
	prepare : function(){
		this.deftxt=document.getElementById('odpowiedz').innerHTML;
		this.remAll();
		var inputs=document.getElementsByTagName('input');
		for(var i=0;i<inputs.length;i++)
			if(inputs[i].type.match(/text/))
			{
				inputs[i].tmpvalue='';
				inputs[i].value='';
				inputs[i].onkeyup=cLiquidCalc.parseInputs;
			}
		var spans=document.getElementsByTagName('span');
		for(i=0;i<spans.length;i++)
			if(id=spans[i].className.match(/help(\d+)/))
			{
				spans[i].idx=id[1];
				spans[i].style.position='relative';
				spans[i].onmouseover=cLiquidCalc.showHelp;
				spans[i].onmouseout=cLiquidCalc.hideHelp;
			}
		this.changeMode(document.getElementById('policz').value);
		if(!location.href.match(/skrypty\.info\.pl\/kalkulator\.html/))
		{
		var div=document.getElementById('download');
		div.innerHTML='Wersja offline';
		}
	},
      
	showHelp : function(){
		//alert('ok');
		helper=document.getElementById('helper');
		if(!helper)
		{
			helper=document.createElement('div');
			helper.id='helper';
			with(helper.style)
		{
			width='200px';
			padding='10px';
			fontSize='10px';
			marginLeft='320px';
			color='#000';
			background='#FEFF8F';
			position='fixed';
			border='solid 1px #999';
		}
			document.body.appendChild(helper);
		}
		with(helper.style)
		{
			display='block';
			top=this.offsetTop+'px';
			left=(this.offsetLeft+this.offsetWidth+5)+'px';
		}
        helper.innerHTML=cLiquidCalc.helpStr[this.idx];
	},
      
		hideHelp : function(){
		helper=document.getElementById('helper');
		if(helper)
		{
			helper.style.display='none';
		}
	},

	changeMode : function(mode){
		this.currentMode=mode;
		var divs=document.getElementsByTagName('div');
		if(divs)
			for(i=0;i<divs.length;i++)
				if(divs[i].id.match(/^forform\d/))
					divs[i].style.display="none";
		document.getElementById('forform'+mode).style.display="block";
		document.getElementById('odpowiedz').innerHTML=this.deftxt;
	},
          
	parseInputs : function(e){
		tt=this.value.replace(/,/,'.').replace(/\s/,'');
		if((tt.match(/^\d+\.?(\d+)?$/))||(tt.length==0))
		{
			this.tmpvalue=tt;
			this.value=this.tmpvalue;
			v=parseFloat(this.value);
			if((this.id.match(/^alko/))&&(v>cLiquidCalc.maxAlco))
				this.value=cLiquidCalc.maxAlco;
			cLiquidCalc.calculate();
			return true;
		}
		this.value=this.tmpvalue;
		cLiquidCalc.calculate();
	},
      
	parseFloat : function(num){
		var tmp=parseFloat(num);
		if(isNaN(tmp))
			tmp=0;
		return tmp;
	},
      
	round : function(num,dec){
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	},
      
	addSel : function(){
		var ilosc=this.parseFloat(document.getElementById('ilosc').value);
		var moc=this.parseFloat(document.getElementById('moc').value);
		if(ilosc<=0)
			document.getElementById('odpowiedz').innerHTML='<p class="blad">Musisz podać ilość liquidu >0<'+'/strong><'+'/p>';
		else{
			var l=document.getElementById('liquidy');
				l.options[l.length]=new Option('Ilość: '+ilosc+' ml; Moc: '+moc+' mg/ml',ilosc+'|'+moc);
				document.getElementById('ilosc').value='';
				document.getElementById('ilosc').tmpvalue='';
				document.getElementById('moc').value='';
				document.getElementById('moc').tmpvalue='';
		}
		cLiquidCalc.calculate();
	},
      
	remAll : function(){
		var select=document.getElementById('liquidy');
		if(select)
			while(select.options.length)
				select.remove(0);
		cLiquidCalc.calculate();
	},

	remSelected : function(){
		var select=document.getElementById('liquidy');
		if(select)
			for(var i=0;i<select.options.length;i++)
				if(select.options[i].selected)
				{
					select.remove(i);
					i-=1 ;
				}
		cLiquidCalc.calculate();
	},
      
		calculate : function(){
			var odpowiedz='';
			switch(this.currentMode)
			{
				case '0':
					var x1=this.parseFloat(document.getElementById('iloscs0').value);
					var y1=this.parseFloat(document.getElementById('mocs0').value);
					var x2=this.parseFloat(document.getElementById('iloscd0').value);
					var y2=this.parseFloat(document.getElementById('mocd0').value);
					var odp=new Array();
					odp[0]=x1+x2;
					if((x1==x2)&&(x1==0))
						odp[1]=0;
					else
						odp[1]=this.round(((x1*y1)+(x2*y2))/(x1+x2),2);
					odpowiedz='<p class="ok">Otrzymasz <strong>'+odp[0]+' ml<'+'/strong> liquidu o mocy <strong>'+odp[1]+' mg/ml<'+'/strong><'+'/p>';
				break;
				case '1':
					var licznik=0;
					var mianownik=0;
					var odp=new Array();
					var select=document.getElementById('liquidy');
					if(select)
						for(var i=0;i<select.options.length;i++)
						{
							q=select.options[i].value.match(/^([\d\.]*)\|([\d\.]*)$/);
							if(q.length==3)
							{
								licznik+=this.parseFloat(q[1])*this.parseFloat(q[2]);
								mianownik+=this.parseFloat(q[1]);
							} else
							{
								select.remove(i);
								i-=1 ;
							}
						}
					odp[0]=mianownik;
					if(mianownik==0)
						odp[1]=0;
					else
						odp[1]=this.round(licznik/mianownik,2);
						odpowiedz='<p class="ok">Otrzymasz <strong>'+odp[0]+' ml<'+'/strong> liquidu o mocy <strong>'+odp[1]+' mg/ml<'+'/strong><'+'/p>';
				break;
				case '2':
					var x1=this.parseFloat(document.getElementById('ilosc1').value);
					var x4=this.parseFloat(document.getElementById('ilosc1liquid').value);
					var y1=this.parseFloat(document.getElementById('moc1').value);
					var z=this.parseFloat(document.getElementById('moc2').value);
					var y2=this.parseFloat(document.getElementById('moc3').value);
					var glic=this.parseFloat(document.getElementById('procent').value);
					var alko=this.parseFloat(document.getElementById('alko1').value);
					var x2=0;

					if(x1<=0)
						odpowiedz='<p class="blad">Nie podano poprawnej (większej od 0) ilości liquidu bazowego<'+'/p>';
					else
					{
							glic2=glic+alko;
							var tmpg=(100)/(100-glic2);
							x2=((x1*y1)/(z*tmpg) - x1 -x4)*((z*tmpg)/(z*tmpg-y2));
							x3=(x1+x2+x4)*glic2/(100-glic2);
						
							if(glic2>=100)
								odpowiedz='<p class="blad">Nie można otrzymać roztworu zawierającego '+glic+'% gliceryny i  '+alko+'% alkoholu<'+'/p>';
							else if(isNaN(x2)&&(y1!=y2))
								odpowiedz='<p class="blad">Podano nieprawidłową ilość stężenia gliceryny i alkoholu<'+'/p>';
							else if(isNaN(x2))
								odpowiedz='<p class="ok">Aby otrzymać liquid zadanej mocy możesz dolewać ile chcesz liquidu "rozcieńczającego" (posiadają te same moce)<'+'/p>';
							else if((x2<0)||(!isFinite(x2)))
								odpowiedz='<p class="blad">Takiej mocy nie uzyskasz drogą mieszania (moce obydwu liquidów są za słabe lub za mocne, aby uzyskać docelową moc, a żądana ilość gliceryny lub alkoholu może być zbyt duża lub zbyt mała)<'+'/p>';
							else
						{
							calosc=x1+x2+x3+x4;
							z1=z2=0;
							if(glic2>0)
						{
							z1=(glic/glic2)*x3;
							z2=x3-z1;
						}
							odpowiedz='<p class="ok">Potrzebujesz ok. <strong>'+this.round(x2,2)+' ml<'+'/strong> liquidu rozcieńczającego (o mocy '+y2+')'+((x3>0)?((z1>0)?', <strong>'+this.round(z1,2)+' ml<'+'/strong> gliceryny':'')+((z2>0)?' oraz <strong>'+this.round(z2,2)+' ml<'+'/strong> alkoholu.':''):'.')+' Razem otrzymasz <strong>'+this.round(calosc,2)+' ml<'+'/strong> liquidu'+((x4>0)?' W całości uwzględniono <strong>'+this.round(x4,2)+' ml<'+'/strong> aromatu.':'');
						}
					}
				break;
				case '3':
					calosc=this.parseFloat(document.getElementById('ilosc4').value);
					var z=this.parseFloat(document.getElementById('moc4').value);
					//var x1=???
					//var x2=???
					var y1=this.parseFloat(document.getElementById('moc6').value);
					var y2=this.parseFloat(document.getElementById('moc7').value);
					var y3=0;
					var glic=this.parseFloat(document.getElementById('procent2').value);
					var alko=this.parseFloat(document.getElementById('alko2').value);
					var glic2=glic+alko;
					var x3=calosc*glic2/100;
					var x4=this.parseFloat(document.getElementById('ilosc2liquid').value);
					var x2=(z*calosc+y1*(x3+x4-calosc))/(y2-y1);
					var x1=calosc-x2-x3-x4;
					if(calosc<=0)
						odpowiedz='<p class="blad">Nie podano poprawnej (większej od 0) ilości liquidu wynikowego<'+'/p>';
				else
					/*if((x1<0)||(x2<0))
						odpowiedz='<p class="blad">Nie można otrzymać danego roztworu<'+'/p>';*/
					//else
					{
					
						if(glic>=100)
							odpowiedz='<p class="blad">Nie można otrzymać roztworu z '+glic+'% gliceryny i '+alko+'% alkoholu<'+'/p>';
						else if(isNaN(x2)&&(y1!=y2))
							odpowiedz='<p class="blad">Podano nieprawidłową ilość stężenia gliceryny lub alkoholu<'+'/p>';
						else if(isNaN(x2))
							odpowiedz='<p class="ok">Mieszając wszystkie płyny zawsze otrzymasz zadaną moc <strong>'+this.round(z,2)+' ml/mg<'+'/strong><'+'/p>';
						else if((x2<0)||(x1<0))
							odpowiedz='<p class="blad">Takiej mocy nie uzyskasz drogą mieszania (moce obydwu liquidów są za słabe lub za mocne, aby uzyskać docelową moc, a żądana ilość gliceryny może być zbyt duża lub zbyt mała)<'+'/p>';
						else
						{
							calosc=x1+x2+x3+x4;
							z1=z2=0;
							if(glic2>0)
							{
								z1=(glic/glic2)*x3;
								z2=x3-z1;
							}
							odpowiedz='<p class="ok">Aby otrzymać '+this.round(calosc,2)+' ml gotowego liquidu potrzebujesz ok. <strong>'+this.round(x1,2)+' ml<'+'/strong> liquidu bazowego (o mocy '+y1+'), <strong>'+this.round(x2,2)+' ml<'+'/strong> liquidu rozcieńczającego (o mocy '+y2+')'+((x3>0)?((z1>0)?', <strong>'+this.round(z1,2)+' ml<'+'/strong> gliceryny':'')+((z2>0)?' oraz <strong>'+this.round(z2,2)+' ml<'+'/strong> alkoholu.':''):'.')+((x4>0)?' W całości uwzględniono <strong>'+this.round(x4,2)+' ml<'+'/strong> aromatu.':'');
						}
					}
				break;
			}
				document.getElementById('odpowiedz').innerHTML=odpowiedz;
		}
	}
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Kalkulator mocy liquidu') }}</div>
				
                <div class="card-body mb-3">
					<form method="get" action="#">
						<h5>Co chcesz policzyć?</h5>
							<select class="form-select form-select-sm mb-3" id="policz" name="policz" onchange="cLiquidCalc.changeMode(this.value);">
								<option value="0">(1) Wynikową moc nikotyny po zmieszaniu dwóch liquidów</option>
								<option value="1">(2) Wynikową moc nikotyny po zmieszaniu kilku liquidów</option>
								<option value="2">(3) Ilość płynu potrzebnego do uzyskania zadanej mocy</option>
								<option value="3">(4) Ilość składników potrzebnych do uzyskania konkretnej ilości liquidu o zadanej mocy</option>
							</select>
						<div id="forform0" class="mb-3">
						<fieldset>
							<h5>Liquid bazowy</h5>
							<label class="col-md-6" for="iloscs0">Ilość liquidu bazowego [ml]:</label>
							<input type="text" class="form-control w-25" name="iloscs0" id="iloscs0" />
							<label class="col-md-6" for="mocs0">Moc liquidu bazowego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
							<input type="text" class="form-control w-25" name="moc0s" id="mocs0" />
					
							<h5>Liquid rozcieńczający</h5>
							<label class="col-md-6" for="iloscd0">Ilość liquidu rozcieńczającego [ml]:</label>
							<input type="text" class="form-control w-25" name="iloscd0" id="iloscd0" />
							<label class="col-md-6" for="mocd0">Moc liquidu rozcieńczającego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
							<input type="text" class="form-control w-25" name="mocd0" id="mocd0" />
						</fieldset>
						</div>
						<div id="forform1" class="mb-3">
						<fieldset>
							<h5>Liquidy w mieszaninie</h5>
							<select name="liquidy" id="liquidy" class="form-select w-25" size="5" multiple="multiple">
							<option value="">---</option>
							</select>
							<input class="btn btn-primary btn-sm" type="button" value="Usuń wybrane" title="Usuń z mieszaniny" onclick="cLiquidCalc.remSelected();"/>
							<input class="btn btn-primary btn-sm" type="button" value="Usuń wszystkie" title="Usuń wszystkie z mieszaniny" onclick="cLiquidCalc.remAll();"/>
						</fieldset>
						<fieldset>
							<h5>Dodaj liquid do mieszaniny</h5>
							<label class="col-md-6" for="ilosc">Ilość liquidu [ml]:</label>
							<input type="text" class="form-control w-25" name="ilosc" id="ilosc" />
							<label class="col-md-6" for="moc">Moc liquidu [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
							<input type="text" class="form-control w-25" name="moc" id="moc" />
							<input class="btn btn-primary btn-sm" type="button" value="Dodaj" title="Dodaj do mieszaniny" onclick="cLiquidCalc.addSel();"/>
						</fieldset>
						</div>
						<div id="forform2" class="mb-3">
						<fieldset>
							<h5>Liquid bazowy</h5>
							<label class="col-md-6" for="ilosc1">Ilość liquidu bazowego [ml]:</label>
							<input type="text" class="form-control w-25" name="ilosc1" id="ilosc1" />
							<label class="col-md-6" for="moc1">Moc liquidu bazowego [mg/ml]: <span class="help help0 btn btn-info btn-dark">?</span></label>
							<input type="text" class="form-control w-25" name="moc1" id="moc1" />
						</fieldset>
						<fieldset>
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
						</fieldset>
						</div>
						<div id="forform3" class="mb-3">
						<fieldset>
							<h5>Chcę uzyskać</h5>
							<label class="col-md-6" for="ilosc4">Ilość liquidu [ml]:</label>
							<input type="text" class="form-control w-25" name="ilosc4" id="ilosc4" />
							<label class="col-md-6" for="moc4">Moc liquidu [mg/ml]:</label>
							<input type="text" class="form-control w-25" name="moc4" id="moc4" />
						</fieldset>
						<fieldset>
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
						</fieldset>
						</div>
						<div id="odpowiedz">
							<p>Tu pojawi się odpowiedź dla zadanego obliczenia...</p>
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