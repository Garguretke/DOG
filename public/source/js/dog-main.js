$(function() {
    $("#swap_theme_button").on('click',function(e){
		if($('html').attr('data-bs-theme') == 'light'){
			$('html').attr('data-bs-theme','dark');
			$(this).find('i').attr('class',"fal fa-sun-bright fa-xl");
			SetCookie('theme','dark',365);
		} else {
			$('html').attr('data-bs-theme','light');
			$(this).find('i').attr('class',"fal fa-moon fa-xl");
			SetCookie('theme','light',365);
		}
    }); 
});

function SetCookie(cname, cvalue, exdays){
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname+"="+cvalue+";"+expires+";path=/";
}
function GetCookie(cname){
	var name = cname+"=", decodedCookie = decodeURIComponent(document.cookie), ca = decodedCookie.split(';');
	for(var i = 0; i < ca.length; i++){
		var c = ca[i];
		while(c.charAt(0) == ' '){
			c = c.substring(1);
		}
		if(c.indexOf(name) == 0){
			return c.substring(name.length, c.length);
		}
	}
	return "";
}