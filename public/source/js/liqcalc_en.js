var cLiquidCalc={
	helpStr : new Array(
		'If the strength is for PG only, VG, nicotine-free flavor or other nicotine-free substance, enter 0 or leave this field blank',
		'The entered value refers to the percentage of glycerin in the resulting solution, not in the added ingredients',
		'Pay special attention to the amount of alcohol added. <strong>Inhalation of alcohol vapors in too high concentrations may cause burns to the respiratory tract and may also damage the inhaler</strong>. The maximum concentration of spirit has been limited to 5% in the solution'
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
	div.innerHTML='Offline version';
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
		document.getElementById('odpowiedz').innerHTML='<p class="blad">You must enter the amount of liquid >0<'+'/strong><'+'/p>';
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
				odpowiedz='<p class="ok">You will receive <strong>'+odp[0]+' ml<'+'/strong> of liquid with a nicotine strength of <strong>'+odp[1]+' mg/ml<'+'/strong><'+'/p>';
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
					odpowiedz='<p class="ok">You will receive <strong>'+odp[0]+' ml<'+'/strong> of liquid with a nicotine strength of <strong>'+odp[1]+' mg/ml<'+'/strong><'+'/p>';
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
					odpowiedz='<p class="blad">The correct amount of base liquid (greater than 0) has not been entered<'+'/p>';
				else
				{
						glic2=glic+alko;
						var tmpg=(100)/(100-glic2);
						x2=((x1*y1)/(z*tmpg) - x1 -x4)*((z*tmpg)/(z*tmpg-y2));
						x3=(x1+x2+x4)*glic2/(100-glic2);
					
						if(glic2>=100)
							odpowiedz='<p class="blad">You cannot obtain a solution containing '+glic+'% glycerin and  '+alko+'% alcohol<'+'/p>';
						else if(isNaN(x2)&&(y1!=y2))
							odpowiedz='<p class="blad">Incorrect amount of glycerin and alcohol concentration was provided<'+'/p>';
						else if(isNaN(x2))
							odpowiedz='<p class="ok">To obtain a liquid of a given strength, you can add as much "diluting" liquid as you want (they have the same strength)<'+'/p>';
						else if((x2<0)||(!isFinite(x2)))
							odpowiedz='<p class="blad">You will not obtain such strength by mixing (the strengths of both liquids are too weak or too strong to obtain the target strength, and the desired amount of glycerin or alcohol may be too large or too small)<'+'/p>';
						else
					{
						calosc=x1+x2+x3+x4;
						z1=z2=0;
						if(glic2>0)
					{
						z1=(glic/glic2)*x3;
						z2=x3-z1;
					}
						odpowiedz='<p class="ok">You need approx. <strong>'+this.round(x2,2)+' ml<'+'/strong> of dilution liquid (with a strength of '+y2+')'+((x3>0)?((z1>0)?', <strong>'+this.round(z1,2)+' ml<'+'/strong> of glycerin':'')+((z2>0)?' and <strong>'+this.round(z2,2)+' ml<'+'/strong> of alcohol.':''):'.')+' In total you will receive <strong>'+this.round(calosc,2)+' ml<'+'/strong> of liquid'+((x4>0)?' The total includes <strong>'+this.round(x4,2)+' ml<'+'/strong> of flavor.':'');
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
					odpowiedz='<p class="blad">The correct amount of resulting liquid (greater than 0) was not provided<'+'/p>';
			else
				/*if((x1<0)||(x2<0))
					odpowiedz='<p class="blad">Nie można otrzymać danego roztworu<'+'/p>';*/
				//else
				{
				
					if(glic>=100)
						odpowiedz='<p class="blad">You cannot obtain a solution containing '+glic+'% glycerin and  '+alko+'% alcohol<'+'/p>';
					else if(isNaN(x2)&&(y1!=y2))
						odpowiedz='<p class="blad">An incorrect amount of glycerin or alcohol concentration was provided<'+'/p>';
					else if(isNaN(x2))
						odpowiedz='<p class="ok">When mixing all liquids you will always get a set strength of <strong>'+this.round(z,2)+' ml/mg<'+'/strong><'+'/p>';
					else if((x2<0)||(x1<0))
						odpowiedz='<p class="blad">You will not obtain such strength by mixing (the strengths of both liquids are too weak or too strong to obtain the target strength, and the desired amount of glycerin may be too large or too small)<'+'/p>';
					else
					{
						calosc=x1+x2+x3+x4;
						z1=z2=0;
						if(glic2>0)
						{
							z1=(glic/glic2)*x3;
							z2=x3-z1;
						}
						odpowiedz='<p class="ok">To obtain '+this.round(calosc,2)+' ml of prepared liquid, you need approx. <strong>'+this.round(x1,2)+' ml<'+'/strong> of base liquid (with a strength of '+y1+'), <strong>'+this.round(x2,2)+' ml<'+'/strong> of diluting liquid (with a strength of '+y2+')'+((x3>0)?((z1>0)?', <strong>'+this.round(z1,2)+' ml<'+'/strong> of glycerin':'')+((z2>0)?' and <strong>'+this.round(z2,2)+' ml<'+'/strong> of alcohol.':''):'.')+((x4>0)?' The total includes <strong>'+this.round(x4,2)+' ml<'+'/strong> of flavor.':'');
					}
				}
			break;
		}
			document.getElementById('odpowiedz').innerHTML=odpowiedz;
	}
}