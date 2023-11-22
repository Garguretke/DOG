var migration_total = 0, migration_current = 0, emu_migrations = [];

function Setup_GetTimeStamp(){
	var date = new Date();
	return date.getFullYear()+'-'+(('00'+(date.getMonth()+1)).slice(-2))+'-'+('00'+date.getDate()).slice(-2)+' '+('00'+date.getHours()).slice(-2)+':'+('00'+date.getMinutes()).slice(-2)+':'+('00'+date.getSeconds()).slice(-2);
}

function Setup_UpdateCount(){
	if(migration_total > 0){
		var percent = (migration_current / migration_total) * 100;
		document.title = "eMU Instalator - "+percent.toFixed(2)+" %";
		$("#migrations_counter").html(migration_current+" z "+migration_total+" postęp "+percent.toFixed(2)+" %");
	} else {
		document.title = "eMU Instalator";
		$("#migrations_counter").html("");
	}
}

function Setup_AppendLog(text){
	$("#listaMigracjiID").val($("#listaMigracjiID").val()+'['+Setup_GetTimeStamp()+'] '+text+'\r\n');
	$("#listaMigracjiID").scrollTop(100000000000);
}

function Setup_LoadMigration(){
	Setup_AppendLog('Rozpoczynanie wczytywania migracji: '+emu_migrations[migration_current].migrationName);
	$.ajax({
		url: "setup.php",
		type: 'POST',
		data: {
			update_start: true,
			migracjaDana: emu_migrations[migration_current]
		},
		dataType: 'json',
		success: function (response){
			if(!response.success){
				$("#btn_update").html('Rozpocznij').prop("disabled", false);
				Setup_AppendLog(response.error);
				return;
			}
			Setup_AppendLog('Zakończono wczytywanie migracji: '+emu_migrations[migration_current].migrationName);
			migration_current++;
			Setup_UpdateCount();
			if(migration_current < migration_total){
				Setup_LoadMigration();
			} else {
				Setup_AppendLog('Odblokowanie dostępu do strony');
				Setup_ToggleAccess();
				$("#btn_update").html('Zakończono').removeClass('btn-success').addClass('btn-danger');
				$('#setup_button_panel').prop('disabled',false);
			}
		},
		error: function (xhr, status, error){
			$("#alertLogowanieID").css("display", "unset");
			$("#btn_update").html('Rozpocznij').prop("disabled", false);
			Setup_AppendLog('Błąd wczytywania migracji: '+emu_migrations[migration_current].migrationName);
			Setup_AppendLog(xhr.responseText);
		}
	});
}

function Setup_ValidateFiles(){
	$.ajax({
		url: "setup.php",
		type: 'POST',
		data: {
			validate_files: true
		},
		dataType: 'json',
		success: function (response){

		},
		error: function (xhr, status, error){

		}
	});
}

function Setup_ToggleAccess(){
	$.ajax({
		url: "setup.php",
		type: 'POST',
		data: {
			toggle_access: true
		},
		dataType: 'json',
		success: function (response){
			Setup_ValidateFiles();
		},
		error: function (xhr, status, error){

		}
	});
}

$(function(){

	$("#btn_update").on('click',function(e){
		$(this).html('Aktualizacja w toku').prop("disabled",true);
		$("#listaMigracjiID").val("");
		$('#setup_button_panel').prop('disabled',true);
		Setup_LoadMigration(emu_migrations);
	});

	$('#setup_button_panel').on('click',function(e){
		if($(this).is(':disabled')) return;
		var app_url = window.location.href.replace('/setup.php','');
		window.location.href = app_url;
	});

	migration_total = Object.keys(emu_migrations).length;
	Setup_UpdateCount();

});
