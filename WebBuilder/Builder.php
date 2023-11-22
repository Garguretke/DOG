<?php
/*	require_once('app\Services\ApiHelper.php');*/

/*	use App\Services\IniFile;*/
	use App\Services\Request;
/*	use App\Services\ApiHelper;*/

	if(!$this->set_php_version("8.1")){
		$this->ave->pause();
		return true;
	}

	$app_name = 'MercjaDOG';

	$this->ave->title("$app_name Builder v$this->app_version");

/*	$config_file = $this->ave->get_file_path("$this->path/WebBuilder/web-builder.ini");
	$config = new IniFile($config_file);
	if(is_null($config->get('USER_EMAIL')) || is_null($config->get('USER_PASSWORD'))){
		$this->ave->pause(" Uzupełnij pola (string)USER_EMAIL oraz (base64)USER_PASSWORD w pliku $config_file");
		return false;
	}

	$api_url = 'https://emu.cti.org.pl/public/api/filemanager';*/

	set_mode:
	$this->ave->clear();
	$this->ave->print_help([
		" 0 - Make beta",
		" 1 - Make stable",
		" 2 - Compile plugins",
		" 3 - Generate guard.ini",
		" 4 - Generate MySQL.ini",
		" 5 - Developer initialize",
		" 6 - Reload cache",
		" ?p - With plugins",
		" ??r - With dumpautoload",
	]);
	$line = $this->ave->get_input(" Typ paczki: ");

	$this->params = [
		'mode' => strtolower($line[0] ?? '?'),
		'plugins' => strtolower($line[1] ?? '?'),
		'dumpautoload' => strtolower($line[2] ?? '?'),
	];

	if(!in_array($this->params['mode'],['0','1','2','3','4','5','6'])) goto set_mode;
	if(!in_array($this->params['plugins'],['?','p'])) goto set_mode;
	if(!in_array($this->params['dumpautoload'],['?','r'])) goto set_mode;

/*	if($this->params['mode'] == '1'){
		$request = new Request();
		$response = $request->post("$api_url/login", [
			'email' => $config->get('USER_EMAIL'),
			'password' => base64_decode($config->get('USER_PASSWORD')),
		]);
		if($response['code'] != 200){
			$this->ave->pause(print_r($response, true));
			return false;
		}
		$request->setHeader(["Authorization: Bearer ".$response['data']['token']]);

		$destinations = [];
		$i = 0;
		$response = $request->post("$api_url/get_folder_items", [
			'path' => '/Optima'
		]);
		if($response['code'] != 200){
			$this->ave->pause(print_r($response, true));
			return false;
		}
		$this->ave->echo();
		$this->ave->echo(" Wersje Optimy:");
		foreach($response['data'] as $item){
			if($item['is_directory'] == 1 && strpos($item['name'], ".") !== false){
				$destinations[$i] = "/Optima/".$item['name'];
				$this->ave->echo(" $i - ".$item['name']);
				$i++;
			}
		}

		$this->ave->echo();
		set_optima:
		$id = $this->ave->get_input(" Wersja: ");
		if(!isset($destinations[$id])) goto set_optima;

		$optima_destination = $destinations[$id];
	}*/

	if(in_array($this->params['mode'],['1','2'])){
		$with_plugins = true;
	} else {
		$with_plugins = ($this->params['plugins'] == 'p');
	}
	if(in_array($this->params['mode'],['1','6'])){
		$dumpautoload = true;
	} else {
		$dumpautoload = ($this->params['dumpautoload'] == 'r');
	}
	$this->ave->clear();

/*	if($this->params['mode'] == '1'){
		$sync_version = (object)[
			'szop' => ApiHelper::getApiVersion('SZOP'),
			'imap' => ApiHelper::getApiVersion('IMAP'),
			'outlook' => ApiHelper::getApiVersion('OUTLOOK'),
		];

		$optima_sync_path = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje dla Klientów/$app_name/Synchronizator Optima/$sync_version->szop");
		if(!file_exists($optima_sync_path)) $this->ave->mkdir($optima_sync_path);
		$szop_files = $this->ave->get_files($optima_sync_path, ['exe']);
		if(count($szop_files) == 0){
			$this->ave->echo(" [$app_name] Nie znaleziono: Synchronizator Optima WS $sync_version->szop");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		} else if(count($szop_files) > 1){
			$this->ave->echo(" [$app_name] Zbyt wiele plików: Synchronizator Optima WS $sync_version->szop");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		}

		$imap_sync_path = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje dla Klientów/$app_name/Synchronizator IMAP/$sync_version->imap");
		if(!file_exists($imap_sync_path)) $this->ave->mkdir($imap_sync_path);
		$imap_files = $this->ave->get_files($imap_sync_path, ['7z', 'gz']);
		if(count($imap_files) < 3){
			$this->ave->echo(" [$app_name] Nie znaleziono: Synchronizator IMAP WS $sync_version->imap");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		} else if(count($imap_files) > 3){
			$this->ave->echo(" [$app_name] Zbyt wiele plików: Synchronizator IMAP WS $sync_version->imap");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		}

		$backup_sync_path = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje dla Klientów/$app_name/Program kopia zapasowa");
		if(!file_exists($backup_sync_path)) $this->ave->mkdir($backup_sync_path);
		$backup_files = $this->ave->get_files($backup_sync_path, ['gz']);
		if(count($backup_files) == 0){
			$this->ave->echo(" [$app_name] Nie znaleziono: Program kopia zapasowa");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		} else if(count($backup_files) > 1){
			$this->ave->echo(" [$app_name] Zbyt wiele plików:	Program kopia zapasowa");
			$this->ave->pause(" Kliknij enter aby zamknąć");
			return false;
		}
	}*/

	if(in_array($this->params['mode'],['0','1','6'])){
		$this->ave->echo(" Czyszczenie cache");
		$this->ave->delete_files('bootstrap/cache', ['php']);

		$this->artisan("down");
		$this->artisan("route:clear");
		$this->artisan("view:clear");
		$this->artisan("clear-compiled");
		$this->artisan("config:clear");
		$this->artisan("up");
/*		$this->artisan("config:cache");
		$this->artisan("route:cache");*/
		$this->artisan("cache:clear");
	}

/*	if($with_plugins){
		$plugin_output = $this->ave->get_file_path("$this->path/../build/$app_name-Plugins/$this->app_version");

		$files = scandir($this->ave->get_file_path("$this->path/plugins_src"));
		foreach($files as $file){
			if($file == '..' || $file == '.' || !is_dir($this->ave->get_file_path("$this->path/plugins_src/$file"))) continue;
			$this->compile_plugin($file, $plugin_output);
		}
	}*/
	if($dumpautoload){
		$this->php("composer.phar dumpautoload");
	}

	if(in_array($this->params['mode'],['0','1'])){
		$this->artisan("guard:generate");
		//file_put_contents($this->ave->get_file_path("$this->temp_folder/auto_update"), "");
		$this->ave->copy($this->ave->get_file_path("$this->path/.htaccess"), $this->ave->get_file_path("$this->temp_folder/.htaccess"));
		//if($this->params['mode'] == '0'){
		//	file_put_contents($this->ave->get_file_path("$this->temp_folder/is_beta"), "");
		//}

		$this->ave->clear();
		$this->ave->echo(" Pakowanie");

		$output_folder = $this->ave->get_file_path("$this->path/../build/$app_name-WebPanel/$this->app_version");
		$zip_file = $this->ave->get_file_path("$output_folder/data.dog");
		if(file_exists($output_folder)) $this->ave->rrmdir($output_folder);
		if(!file_exists($output_folder)) $this->ave->mkdir($output_folder);

		$this->ave->copy($this->ave->get_file_path("$this->path/extract.php"), $this->ave->get_file_path("$output_folder/extract.php"));

		$files = $this->ave->get_arguments_folders([
			"$this->temp_folder/*",
			"$this->path/vendor",
			"$this->path/app",
			"$this->path/bootstrap",
			"$this->path/config",
			"$this->path/database",
			"$this->path/public",
			"$this->path/resources",
			"$this->path/routes",
			"$this->path/artisan",
			"$this->path/server.php",
			"$this->path/composer.json",
			"$this->path/guard.ini",
/*			"$this->path/MySQL.ini",
			"$this->path/backup-ftp.php",*/
			"$this->path/version",
			"$this->path/webpack.mix.js",
			"$this->path/package.json",
		]);

		$exclude = $this->ave->get_file_path("$this->path/exclude.lst");

		system("7z a -tzip -mx5 -r \"$zip_file\" $files -x@\"$exclude\"");

		$this->artisan("config:cache");
		$this->artisan("route:cache");

		//if($this->params['mode'] == '0'){
		//	$nas_output = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje do testów/$app_name/WebPanel");
		//	$this->ave->rrmdir($nas_output);
		//	$destination_folder = $this->ave->get_file_path("$nas_output/$this->app_version");
		//	if(!file_exists($destination_folder)) $this->ave->mkdir($destination_folder);
		//	$this->ave->rename($this->ave->get_file_path("$output_folder/data.dog"), $this->ave->get_file_path("$destination_folder/data.dog"));
		//	$this->ave->rename($this->ave->get_file_path("$output_folder/extract.php"), $this->ave->get_file_path("$destination_folder/extract.php"));
		//	if(file_exists($output_folder)) $this->ave->rrmdir($output_folder);

		//	$request = new Request();
		//	$request->post("https://emu.cti.org.pl/public/api/send-message", [
		//		'email' => $config->get('USER_EMAIL'),
		//		'password' => base64_decode($config->get('USER_PASSWORD')),
		//		'user_id' => 143,
		//		'message' => "<i>Nowa wersja do testów $app_name v$this->app_version jest dostępna na dysku programistów</i>",
		//	], true);
		//} else if($this->params['mode'] == '1'){
		//	$nas_output = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje dla Klientów/$app_name/WebPanel");
		//	$this->ave->rrmdir($nas_output);
		//	$destination_folder = $this->ave->get_file_path("$nas_output/$this->app_version");
		//	if(!file_exists($destination_folder)) $this->ave->mkdir($destination_folder);
		//	$this->ave->rename($this->ave->get_file_path("$output_folder/data.dog"), $this->ave->get_file_path("$destination_folder/data.dog"));
		//	$this->ave->rename($this->ave->get_file_path("$output_folder/extract.php"), $this->ave->get_file_path("$destination_folder/extract.php"));

		//	$emu_package_path = $this->ave->get_file_path("//NAS-PROG/Dane/Wersje/Wersje dla Klientów/$app_name/WebPanel/$this->app_version");
		//	$emu_package_files = $this->ave->get_files($emu_package_path, ['emu', 'php']);
		//	if(count($emu_package_files) < 2){
		//		$this->ave->echo(" [$app_name] Nie znaleziono: WebPanel v$this->app_version");
		//		$this->ave->pause(" Kliknij enter aby zamknąć");
		//		return false;
		//	} else if(count($emu_package_files) > 2){
		//		$this->ave->echo(" [$app_name] Zbyt wiele plików: WebPanel v$this->app_version");
		//		$this->ave->pause(" Kliknij enter aby zamknąć");
		//		return false;
		//	}

		//	$backup_version = str_replace(["eMU-BACKUP_v", "_LINUX.tar.gz"], "", pathinfo($backup_files[0], PATHINFO_BASENAME));
		//	$imap_version = str_replace(["eMU-MAUPA_v", "_LINUX.tar.gz", "_WINDOWS_FULL.7z", "_WINDOWS_UPDATE.7z", "_WS$sync_version->imap"], "", pathinfo($imap_files[0], PATHINFO_BASENAME));
		//	$szop_version = str_replace(["setup_Synchronizator_eMU", "_WS$sync_version->szop"], "", pathinfo($szop_files[0], PATHINFO_FILENAME));

		//	$pdf_file_path = $this->ave->get_file_path("$this->path/_Documents/publish/pdf");
		//	if(!file_exists($pdf_file_path)) $this->ave->mkdir($pdf_file_path);
		//	$pdf_files = $this->ave->get_files($pdf_file_path, ['pdf']);

		//	$zip_lite = $this->ave->get_file_path("$output_folder/$app_name"."_v".$this->app_version."_".date("d.m.Y")."_Lite.zip");
		//	$zip_full = $this->ave->get_file_path("$output_folder/$app_name"."_v".$this->app_version."_".date("d.m.Y")."_Full.zip");

		//	$zip = new ZipArchive();
		//	$res = $zip->open($zip_lite, ZipArchive::CREATE);
		//	if($res !== true){
		//		$this->ave->echo(" Wystąpił błąd podczas tworzenia pliku: \"$zip_lite\" Błąd: ".$zip->getStatusString());
		//		$this->ave->pause(" Kliknij enter aby zamknąć");
		//		return false;
		//	}
		//	$name = pathinfo($zip_lite, PATHINFO_BASENAME);
		//	$this->ave->echo(" Tworzenie pliku: \"$name\"");
		//	foreach($emu_package_files as $file){
		//		$name = "WebPanel/".pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	foreach($pdf_files as $file){
		//		$name = pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	$zip->close();

		//	$zip = new ZipArchive();
		//	$res = $zip->open($zip_full, ZipArchive::CREATE);
		//	if($res !== true){
		//		$this->ave->echo(" Wystąpił błąd podczas tworzenia pliku: \"$zip_full\" Błąd: ".$zip->getStatusString());
		//		$this->ave->pause(" Kliknij enter aby zamknąć");
		//		return false;
		//	}
		//	$name = pathinfo($zip_full, PATHINFO_BASENAME);
		//	$this->ave->echo(" Tworzenie pliku: \"$name\"");
		//	foreach($emu_package_files as $file){
		//		$name = "WebPanel/".pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	foreach($szop_files as $file){
		//		$name = "Synchronizator Optima/".pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	foreach($imap_files as $file){
		//		$name = "Synchronizator IMAP/".pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	foreach($backup_files as $file){
		//		$name = "Program kopia zapasowa/".pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	foreach($pdf_files as $file){
		//		$name = pathinfo($file, PATHINFO_BASENAME);
		//		$zip->addFile($file, $name);
		//		$zip->setCompressionName($name, ZipArchive::CM_STORE, 0);
		//	}
		//	$zip->close();

		//	$request->post("$api_url/delete", ['path' => "$optima_destination/$app_name"]);

		//	$request->post("$api_url/create_folder", ['path' => "$optima_destination", 'name' => $app_name]);
		//	$request->post("$api_url/create_folder", ['path' => "$optima_destination/$app_name", 'name' => "Program kopia zapasowa"]);
		//	$request->post("$api_url/create_folder", ['path' => "$optima_destination/$app_name", 'name' => "Synchronizator IMAP"]);
		//	$request->post("$api_url/create_folder", ['path' => "$optima_destination/$app_name", 'name' => "Synchronizator Optima"]);
		//	$request->post("$api_url/create_folder", ['path' => "$optima_destination/$app_name", 'name' => "Web Panel"]);

		//	$summary = implode("\r\n", [
		//		"<b>Wersja Optimy:</b> ".str_replace("/Optima/", "", $optima_destination),
		//		"<b>Data wydania:</b> ".date('Y-m-d H:i:s'),
		//		"",
		//		"<b>WebPanel:</b> v$this->app_version",
		//		"<b>Synchronizator Optima:</b> v$szop_version WS $sync_version->szop",
		//		"<b>Synchronizator IMAP:</b> v$imap_version WS $sync_version->imap",
		//		"<b>Wtyczka Outlook:</b> WS $sync_version->outlook",
		//		"<b>Program kopia zapasowa:</b> v$backup_version",
		//	]);

		//	$request->post("$api_url/create_file", [
		//		'path' => "$optima_destination/$app_name",
		//		'name' => "$app_name.txt",
		//		'content' => base64_encode(strip_tags($summary)),
		//		'content_type' => "base64",
		//	]);

		//	foreach($backup_files as $file){
		//		$name = pathinfo($file, PATHINFO_BASENAME);
		//		$this->ave->echo(" Wgrywanie \"$name\"");
		//		$request->post("$api_url/create_file", [
		//			'path' => "$optima_destination/$app_name/Program kopia zapasowa",
		//			'name' => $name,
		//			'content' => base64_encode(file_get_contents($file)),
		//			'content_type' => "base64",
		//		]);
		//	}

		//	foreach($imap_files as $file){
		//		$name = pathinfo($file, PATHINFO_BASENAME);
		//		$this->ave->echo(" Wgrywanie \"$name\"");
		//		$request->post("$api_url/create_file", [
		//			'path' => "$optima_destination/$app_name/Synchronizator IMAP",
		//			'name' => $name,
		//			'content' => base64_encode(file_get_contents($file)),
		//			'content_type' => "base64",
		//		]);
		//	}

		//	foreach($szop_files as $file){
		//		$name = pathinfo($file, PATHINFO_BASENAME);
		//		$this->ave->echo(" Wgrywanie \"$name\"");
		//		$request->post("$api_url/create_file", [
		//			'path' => "$optima_destination/$app_name/Synchronizator Optima",
		//			'name' => $name,
		//			'content' => base64_encode(file_get_contents($file)),
		//			'content_type' => "base64",
		//		]);
		//	}

		//	$name = pathinfo($zip_lite, PATHINFO_BASENAME);
		//	$this->ave->echo(" Wgrywanie \"$name\"");
		//	$request->post("$api_url/create_file", [
		//		'path' => "$optima_destination/$app_name/Web Panel",
		//		'name' => $name,
		//		'content' => base64_encode(file_get_contents($zip_lite)),
		//		'content_type' => "base64",
		//	]);

		//	$name = pathinfo($zip_full, PATHINFO_BASENAME);
		//	$this->ave->echo(" Wgrywanie \"$name\"");
		//	$request->post("$api_url/create_file", [
		//		'path' => "$optima_destination/$app_name",
		//		'name' => $name,
		//		'content' => base64_encode(file_get_contents($zip_full)),
		//		'content_type' => "base64",
		//	]);

		//	$response = $request->post("$api_url/logout");

		//	$request = new Request();
		//	foreach([143, 49] as $user_id){
		//		$request->post("https://emu.cti.org.pl/public/api/send-message", [
		//			'email' => $config->get('USER_EMAIL'),
		//			'password' => base64_decode($config->get('USER_PASSWORD')),
		//			'user_id' => $user_id,
		//			'message' => "<b>Nowa wersja $app_name została wydana.</b>\r\n$summary",
		//		], true);
		//	}

		//	if(file_exists($output_folder)) $this->ave->rrmdir($output_folder);
		//}
	}

	if($this->params['mode'] == '3'){
		$this->artisan("guard:generate");
	}
	if($this->params['mode'] == '4'){
		$this->artisan("schema:generate");
	}
	if($this->params['mode'] == '5'){
		$this->ave->echo("Tworzenie wymaganych folderów aplikacji");
		$this->ave->delete_files('bootstrap/cache', ['php']);
		$folders = [
			"$this->path/backup/setup",
			"$this->path/backup/recovery",
			"$this->path/public/upgrade",
			"$this->path/bootstrap/cache",
			"$this->path/storage/logs",
			"$this->path/storage/framework/views",
			"$this->path/storage/framework/testing",
			"$this->path/storage/framework/sessions",
			"$this->path/storage/framework/cache/data",
			"$this->path/storage/app/public",
			"$this->path/storage/app/cache",
		];
		foreach($folders as $folder){
			$this->ave->mkdir($this->ave->get_file_path($folder));
		}
	}

	$this->ave->pause("Operacja zakończona, kliknij enter aby zamknąć");
?>
