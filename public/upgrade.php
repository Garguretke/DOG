<?php
	declare(strict_types=1);

	if(file_exists('../data.dog')){
		if(file_exists('../extract.php')){
			require_once('../extract.php');
		} else {
			die("Nie mozna uruchomić narzędzia !<br><br>"."Plik extract.php nie istnieje.");
		}
		return;
	}
	if(file_exists('../extract.php') && !file_exists('../WebBuilder')){
		unlink('../extract.php');
	}

	chdir(__DIR__.'/..');

	const APP_NAME = 'MercjaDOG';
	const PHP_REQUIRED = '8.1, 8.2';

	$path = [
		'guard' => 'backup/setup/guard.ini',
		'pack' => 'backup/setup/data.dog',
		'hash' => 'backup/setup/data.dog.md5',
		'ini_file' => 'backup/setup/IniFile.php',
		'guard_driver' => 'backup/setup/GuardDriver.php',
		'logs_driver' => 'backup/setup/Logs.php',
		'version' => 'backup/setup/version',
	];

	$init_errors = [];
	if(file_exists($path['pack'])){
		$has_update = true;
	} else {
		$has_update = false;
	}

	if($has_update){
		foreach($path as $p){
			if(!file_exists($p)){
				array_push($init_errors,"Plik $p nie istnieje.");
			}
		}
	}

	if(!file_exists($path['ini_file'])){
		die("Nie mozna uruchomić narzędzia !<br><br>"."Plik ".$path['ini_file']." nie istnieje.");
	}

	require_once($path['ini_file']);
	use App\Services\IniFile as IniFile;

	class UpdaterEMU {

		public string $location;

		public function __construct(string $location = __DIR__){
			$this->location = $location;
		}

		public function get_php_version() : int {
			$version = explode('.',str_replace(preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})/', '', PHP_VERSION), "", PHP_VERSION));
			return ($version[0] * 10000 + $version[1] * 100 + $version[2]);
		}

		public function parse_size_value(string $value) : int {
			if(preg_match('/^(\d+)(.)$/',$value,$matches)){
				if($matches[2] == 'G'){
					return (int)$matches[1] * 1024 * 1024 * 1024;
				} else if($matches[2] == 'M'){
					return (int)$matches[1] * 1024 * 1024;
				} else if($matches[2] == 'K'){
					return (int)$matches[1] * 1024;
				}
			}
			return $matches[1];
		}

		public function check_collision() : bool {
			try {
				if(@file_exists("$this->location/../../.htaccess")) return true;
				return false;
			}
			catch(Exception $e){
				return false;
			}
		}

		public function validate(?IniFile $env) : array {
			$errors = [];
			$warnings = [];

			$PHP_VERSION_NUMBER = $this->get_php_version();
			$extensions = get_loaded_extensions();

			if($PHP_VERSION_NUMBER < 80100 || $PHP_VERSION_NUMBER > 80299) array_push($errors,"Nieprawidłowa wersja PHP wymagana 8.1, 8.2");

			if($this->parse_size_value(ini_get('memory_limit')) < 128 * 1024 * 1024) array_push($errors,"Ustawienie parametru memory_limit minimalne 128M wykryte ".ini_get('memory_limit'));
			if($this->parse_size_value(ini_get('post_max_size')) < 64 * 1024 * 1024) array_push($errors,"Ustawienie parametru post_max_size minimalne 64M wykryte ".ini_get('post_max_size'));
			if($this->parse_size_value(ini_get('upload_max_filesize')) < 20 * 1024 * 1024) array_push($errors,"Ustawienie parametru upload_max_filesize minimalne 20M wykryte ".ini_get('upload_max_filesize'));
			if(ini_get('max_execution_time') < 180) array_push($errors,"Ustawienie parametru max_execution_time minimalne 180 wykryte ".ini_get('max_execution_time'));

			if(!in_array("tokenizer",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: Tokenizer");
			if(!in_array("bcmath",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: BCMath");
			if(!in_array("ctype",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: Ctype");
			if(!in_array("fileinfo",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: Fileinfo");
			if(!in_array("curl",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: cURL");
			if(!in_array("dom",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: dom");
			if(!in_array("gd",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: gd");
			if(!in_array("json",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: JSON");
			if(!in_array("mbstring",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: Mbstring");
			if(!in_array("openssl",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: OpenSSL");
			if(!in_array("PDO",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: PDO");
			if(!in_array("zip",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: ZipArchive Extension");
			if(!in_array("xml",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: XML");
			if(!in_array("soap",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: Soap");
			if(!in_array("imap",$extensions)) array_push($errors,"Brak wymaganego rozszerzenia: IMAP");

			if(function_exists('apache_get_modules')){
				$modules = apache_get_modules();
				if(!in_array('mod_rewrite',$modules)) array_push($errors,"Brak wymaganego modułu mod_rewrite");
				if(!in_array('mod_headers',$modules)) array_push($errors,"Brak wymaganego modułu mod_headers");
			}

			if(!is_null($env)){
				if(strpos($env->get('APP_URL'), "http://") === false && strpos($env->get('APP_URL'), "https://") === false) array_push($warnings,"Wartość w pliku .env APP_URL jest nieprawidłowa: Brak zdefiniowanego protokołu http(s)://");
				if($env->get('APP_FORCE_SSL',false) && strpos($env->get('APP_URL'), "https://") === false) array_push($warnings,"Wartość w pliku .env APP_URL jest nieprawidłowa: Wymuszenie https wymaga by wartość APP_URL zawierała https://");
				if($env->get('APP_ENV','local') != 'local') array_push($warnings,"Wartość w pliku .env APP_ENV jest nieprawidłowa: Oczekiwana wartość local");
				if(!$env->get('APP_ACCESS_API',true)) array_push($warnings,"Dostęp do strony przez API jest zablokowany");
				$is_beta = file_exists(__DIR__.'/../is_beta');
				if(!$is_beta){
					if(is_null($env->get('SESSION_DOMAIN','_blank'))) array_push($warnings,"Nie skonfigurowano domeny dla cookies (Ustawienia > Sesja > Domena aplikacji)");
					if(!$env->get('APP_FORCE_SSL',false)) array_push($warnings,"Tryb 'Wymuś SSL' nie jest włączony");
					if(!$env->get('APP_COMMANDER_MAIN',true)) array_push($warnings,"Tryb 'Logi systemowe (Aplikacja)' jest wyłączony");
					if(!$env->get('APP_COMMANDER_API',true)) array_push($warnings,"Tryb 'Logi systemowe (API)' jest wyłączony");
					if(!$env->get('APP_COMMANDER_JAVASCRIPT',true)) array_push($warnings,"Tryb 'Logi systemowe (JavaScript)' jest wyłączony");
					if($env->get('APP_SEND_QUERY_ON_BST',false)) array_push($warnings,"Tryb 'Wysyłaj zapytanie na BST' jest włączony");
					if($env->get('APP_DUCK_TEST_MODE',false)) array_push($warnings,"Tryb testowy dla Tryb kaczka jest włączony");
					if($env->get('APP_FUNCTION_PERFORMANCE_LOG',false)) array_push($warnings,"Dziennik wydajności funkcji jest włączony");
					if($env->get('APP_PLUGIN_DEVELOPER',false)) array_push($warnings,"Pluginy działają w trybie developera");
					if($env->get('APP_EXPERIMENTAL',false)) array_push($warnings,"Funkcje eksperymentalne są włączone");
					if($env->get('APP_DEBUG',false)) array_push($warnings,"Tryb debug jest włączony");
					if($env->get('APP_DEBUG_REQUEST_USER',false)) array_push($warnings,"Tryb 'Request log (User)' jest włączony");
					if($env->get('APP_DEBUG_REQUEST_API',false)) array_push($warnings,"Tryb 'Request log (API)' jest włączony");
					if($env->get('APP_LOG_LEVEL','info') != 'info') array_push($warnings,"Wartość w pliku .env APP_LOG_LEVEL ".$env->get('APP_LOG_LEVEL','info')." jest niezalecana: Zalecana wartość info");
				}
			}

			if(!in_array("imagick",$extensions)) array_push($warnings,"Brak zalecanego rozszerzenia: Imagick (ImageMagick)");
			if(!function_exists('opcache_get_status')) array_push($warnings,"Brak zalecanego rozszerzenia: OPcache");

			if($this->check_collision()) array_push($warnings,"Nad katalogiem aplikacji wykryto plik .htaccess może to powodować problemy z funkcjonowaniem aplikacji.<br>Upewnij się że nad katalogiem aplikacji nie jest zainstalowany inny program np. WordPress");

			if(function_exists('opcache_get_status')){
				if($this->parse_size_value(ini_get('memory_limit')) < 256 * 1024 * 1024) array_push($warnings,"Ustawienie parametru memory_limit dla aktywnego OPcache zalecane 256M wykryte ".ini_get('memory_limit'));
			}

			$apacheCheck = false;
			if(in_array("apache2handler",$extensions)) $apacheCheck = true;
			if(in_array("apache2filter",$extensions)) $apacheCheck = true;
			if(in_array("apache",$extensions)) $apacheCheck = true;
			if(in_array("litespeed",$extensions)) $apacheCheck = true;
			if(in_array("cgi-fcgi",$extensions)) $apacheCheck = true;
			if(!$apacheCheck){
				array_push($warnings,"Serwer API nie został rozpoznany oczekiwano: apache2handler, apache2filter, apache, litespeed, cgi-fcgi");
			}

			$system_type = php_uname('s');
			if(!in_array($system_type,['Linux','FreeBSD'])){
				array_push($warnings,"Środowisko pracy '$system_type' nie jest zalecane, zalecane środowisko pracy 'Linux'");
			}

			if(file_exists('public/uploads')) array_push($errors,"Wykryto stary folder załączników /public/uploads Wymagane manualne przeniesienie zawartości do /storage/app/public");

			$file = "$this->location/permission_check";

			$createFileCheck = false;
			try {
				file_put_contents($file, $file);
				if(file_exists($file)) $createFileCheck = true;
			}
			catch(Exception $e){
				$createFileCheck = false;
			}

			$deleteFileCheck = false;
			if(file_exists($file)){
				try {
					unlink($file);
				}
				catch(Exception $e){
					$createFileCheck = false;
				}
				if(!file_exists($file)) $deleteFileCheck = true;
			}

			$permissions = $createFileCheck && $deleteFileCheck;
			if(!$permissions) array_push($errors,"Brak uprawnień zapisu na dysku");

			return [
				'errors' => $errors,
				'warnings' => $warnings,
			];
		}
	}

	if(file_exists($path['version'])){
		$app_version_pack = preg_replace('/\s+/','',file_get_contents($path['version']));
	} else {
		$app_version_pack = '0.0.0.0';
	}

	if(file_exists('version')){
		$app_version_installed = preg_replace('/\s+/','',file_get_contents('version'));
	}

	if(!isset($app_version_installed)){
		if(file_exists('.env')){
			$env = new IniFile('.env');
			if($env->isSet('APP_VERSION')) $app_version_installed = $env->get('APP_VERSION');
		}
	}

	if(!isset($app_version_installed)){
		if(file_exists('public/update.php')){
			$content = file_get_contents('public/update.php');
			if(preg_match_all('/APP_VERSION = \'(.*)\';/i',$content,$matches,PREG_SET_ORDER,0)){
				if(isset($matches[0][1])) $app_version_installed = $matches[0][1];
			}
		}
	}

	if(!isset($app_version_installed)){
		$app_version_installed = '0.0.0.0';
	}

	$version = explode(".",$app_version_pack);
	$version_number_pack = intval($version[0] ?? 0)*1000000 + intval($version[1] ?? 0)*10000 + intval($version[2] ?? 0)*100 + intval($version[3] ?? 0);

	$version = explode(".",$app_version_installed);
	$version_number_installed = intval($version[0] ?? 0)*1000000 + intval($version[1] ?? 0)*10000 + intval($version[2] ?? 0)*100 + intval($version[3] ?? 0);

	if($has_update){
		if(file_exists('.env')){
			$env = new IniFile('.env');
		}
		$updater = new UpdaterEMU(__DIR__);
		$validation = $updater->validate($env ?? null);
		$init_errors = array_merge($init_errors,$validation['errors']);

		if(file_exists('.env')){
			if($version_number_installed == 0){
				array_push($init_errors,"Nie udało się zidentyfikować programu MercjaDOG we wskazanym katalogu.");
			} else if($version_number_installed >= 2000000){
				array_push($init_errors,"Odczytana wersja programu eMU wykracza poza zakres.");
			}
		}
		if($version_number_pack == 0){
			array_push($init_errors,"Paczka nie zawiera poprawnej informacji wersji.");
		} else if($version_number_pack < 1000000 || $version_number_pack >= 2000000){
			array_push($init_errors,"Wykryta paczka nie jest zgodna z nowym systemem aktualizacji. Wymagana wersja v1.0.0.0+ oraz poniżej wersji v2.0.0.0");
		}
	} else {
		array_push($init_errors,"Brak aktualizacji do zainstalowania.");
	}
?>
<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="./public/upgrade/bootstrap.min.css">
		<link rel="stylesheet" href="./public/upgrade/upgrade.css?ver=<?php echo rand(1000,9999); ?>">
		<script src="./public/upgrade/jquery-3.6.1.min.js"></script>
		<script src="./public/upgrade/upgrade.js?ver=<?php echo rand(1000,9999); ?>"></script>
		<link rel="shortcut icon" href="favicon2022.ico">
		<title><?php echo APP_NAME; ?> Aktualizacja</title>
	</head>
	<body class="disable-text-select">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<br>
					<h1 class="text-center">
						<?php echo APP_NAME; ?> <span id="app_title">Aktualizacja plików strony</span>
					</h1>
					<br><br><br>
					<center>
						<table id="version_table">
							<tr>
								<th>Wersja aplikacji</th>
								<th>Wersja paczki</th>
								<th>Wykryta wersja PHP</th>
								<th>Wymagana wersja PHP</th>
								<th>Wymagana wersja asysty</th>
							</tr>
							<tr>
								<td><?php echo $app_version_installed; ?><br>&nbsp;</td>
								<td><?php echo (($app_version_pack == '0.0.0.0') ? 'Brak' : $app_version_pack); ?><br>&nbsp;</td>
								<td><?php echo str_replace(preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})/', '', PHP_VERSION), "", PHP_VERSION); ?><br>&nbsp;</td>
								<td><?php echo PHP_REQUIRED; ?><br>&nbsp;</td>
							</tr>
						</table>
					</center>
					<br>
				</div>
			</div>
			<center>
				<div id="app_content">
					<?php if(!empty($init_errors)){ ?>
						<?php foreach($init_errors as $error){ ?>
							<div class="col-xs-12">
								<div class="alert alert-danger" role="alert"><strong>Błąd</strong> <?php echo $error; ?></div>
							</div>
						<?php } ?>
					<?php } else { ?>
						<?php foreach($validation['warnings'] as $warning){ ?>
							<div class="col-xs-12">
								<div class="alert alert-warning" role="alert"><strong>Ostrzeżenie</strong> <?php echo $warning; ?></div>
							</div>
						<?php } ?>
						<div class="form-group">
							<button type="button" id="btn_init_extract" class="btn btn-success center-block">Rozpocznij</button>
						</div>
						<div id="errors"></div><br>
						<div class="col-xs-12">
							<h4>Uwaga !</h4>
							<p>Wszystkie nierozpoznane pliki w katalogach: app, config, database, resources, routes, vendor oraz public zostaną przeniesione do folderu _deleted i zostaną usunięte po 14 dniach.</p>
						</div>
					<?php } ?>
				</div>
			</center>
			<div style="position:absolute;top:10px;left:10px;display:flex">
				<input type="button" class="btn btn-primary" id="upgrade_button_panel" value="Strona główna" style="width:120px">
				<a href="logs.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Logi</a>
				<a href="recovery.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Odzyskiwanie</a>
				<a href="setup.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Instalator</a>
			</div>
		</div>
		<script type="text/javascript">
			var uri = window.location.toString();
			if(uri.indexOf("?") > 0){
				var clean_uri = uri.substring(0,uri.indexOf("?"));
				window.history.replaceState({},document.title,clean_uri);
			}
		</script>
	</body>
</html>
