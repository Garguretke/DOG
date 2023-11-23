<?php

declare(strict_types=1);

const APP_NAME = 'MercjaDOG';
const PHP_REQUIRED = '8.1, 8.2';
const SETUP_LOG_FILE = "../storage/logs/setup.log";

$version_file = "../version";
if(!file_exists($version_file)) die("Nie udało się odczytać wersji programu: Plik version nie istnieje.");

$handle = fopen($version_file,"r");
$app_version = preg_replace('/\s+/', '', fread($handle, filesize($version_file)));
fclose($handle);

require __DIR__.'/../bootstrap/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

use Carbon\Carbon;
use App\Models\User;
use App\Services\SetupDriver;
use App\Services\SetupHelper;
use App\Services\Logs;

if($_SERVER['REQUEST_METHOD'] === 'POST' || ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['dev']) || (isset($_GET['auto_update']) && file_exists("../auto_update"))))){

	if(isset($_POST['update_start'])){
		try {
			$form_data['success'] = true;
			$logs = new Logs(SETUP_LOG_FILE);
			$logs->write("Rozpoczynanie wczytywania migracji: ".$_POST['migracjaDana']['filepath']);
			if(($err = $kernel->call('migrate', ['--path' => str_replace('../', './', $_POST['migracjaDana']['filepath'])])) !== 0){
				$logs->write("Bład migracji: ".$_POST['migracjaDana']['filepath'].' '.$err);
				throw new Exception('Bład migracji: ['.$_POST['migracjaDana']['filepath'].'] '.$err);
			}
			$logs->write("Zakończono wczytywanie migracji: ".$_POST['migracjaDana']['filepath']);
			$form_data['migration'] = $_POST['migracjaDana']['migrationName'];
			echo json_encode($form_data);
		}
		catch(Exception $e){
			$form_data['success'] = false;
			$form_data['error'] = $e->getMessage();
			echo json_encode($form_data);
		}
		return;
	}

	if(isset($_POST['validate_files'])){
		if(file_exists('../_Builder')) return;
		$kernel->call('up', []);
		if(pathinfo(getcwd(),PATHINFO_FILENAME) != 'public') chdir('public');
		$setup = new SetupDriver();
		$now = Carbon::now();
		$last_verification = $setup->env->get('APP_NEXT_VERIFICATION',Carbon::now()->addDays(-7)->format("Y-m-d H:i:s"));
		if($now->gt(Carbon::parse($last_verification))){
			$logs = new Logs(SETUP_LOG_FILE);
			$logs->write("Uruchomienie weryfikacji spójności plików");
			$setup->env->update(['APP_NEXT_VERIFICATION' => $now->addDays(1)->format("Y-m-d H:i:s")], true);
			$setup->checkValidateFiles();
		}
		return;
	}

	if(isset($_POST['toggle_access'])){
		$kernel->call('up', []);
		if(pathinfo(getcwd(),PATHINFO_FILENAME) != 'public') chdir('public');
		$setup = new SetupDriver();
		$logs = new Logs(SETUP_LOG_FILE);
		$logs->write("Odblokowanie dostępu do strony");
		$setup->toggleAccess(true);
		$logs->write("Weryfikacja .env");
		$setup->checkRequired($app_version);
		$logs->write("Odświeżenie cache");
		SetupHelper::refreschCache($kernel,null,'setup.php',$setup->IsCommanderExists(),$setup->IsUserCacheExists());
		if(file_exists("../auto_update")) unlink("../auto_update");
		return;
	}

	if(isset($_POST['check_connect'])){
		$logs = new Logs(SETUP_LOG_FILE);
		try {
			$setup = new SetupDriver();

			// $serial = $_POST['serial'];
			$email = $_POST['email'];

			$passwordAdmin = $_POST['password'];
			$passwordConfirm = $_POST['password_confirm'];

			if(empty($passwordAdmin) || empty($passwordConfirm)) throw new Exception("Należy podać nowe początkowe hasło administratora.");
			if(strcmp($passwordAdmin, $passwordConfirm) !== 0) throw new Exception("Podane hasła nie są identyczne.");
			if(strlen($passwordAdmin) < 6) throw new Exception("Hasło musi mieć conajmniej 6 znaków.");

			$logs->write("Wykonywanie testu połączenia z bazą danych");
			if(!$setup->connectSQL($_POST['DB_HOST'], $_POST['DB_DATABASE'], $_POST['DB_USERNAME'], $_POST['DB_PASSWORD'], intval($_POST['DB_PORT']), $_POST['DB_CONNECTION'])){
				throw new Exception("Błąd połączenia z bazą danych.");
			}

			if($setup->EnvExists()) throw new Exception("Plik konfiguracyjny .env już istnieje!");

			$logs->write("Utworzenie pliku .env");
			$setup->EnvCreate([
				'db_hostname' => $_POST['DB_HOST'],
				'db_port' => $_POST['DB_PORT'],
				'db_database' => $_POST['DB_DATABASE'],
				'db_username' => $_POST['DB_USERNAME'],
				'db_password' => $_POST['DB_PASSWORD'],
				'db_connection' => $_POST['DB_CONNECTION'],
				'app_url' => $_POST['APP_URL'],
				'session_domain' => $_POST['SESSION_DOMAIN'],
				'email' => $email,
				// 'serial' => $serial,
				// 'send_crash_report' => $_POST['send_crash_report'],
				// 'erp_system_type' => $_POST['erp_system_type'],
			]);

			$logs->write("Inicjacja wstępna");
			$kernel->call('config:clear', []);
			$kernel->call('key:generate', []);
			$kernel->call('config:clear', []);
			// $kernel->call('jwt:secret', []);

			$migrationsInstall = [
				'./database/migrations/2014/01'
			];

			foreach($migrationsInstall as $migration){
				$logs->write("Rozpoczynanie wczytywania migracji: ".$migration);
				if(($err = $kernel->call('migrate', ['--path' => $migration])) !== 0){
					$logs->write("Bład migracji: ".$migration.' '.$err);
					throw new Exception('Bład migracji: ['.$migration.'] '.$err);
				}
				$logs->write("Zakończono wczytywanie migracji: ".$migration);
			}

			$logs->write("Utworzenie użytkowników systemowych");

			$user = new User();
			$user->name = 'admin';
			$user->email = $email;
			$user->password = bcrypt($passwordAdmin);
			$user->save();

			$kernel->call('view:clear', []);
			$kernel->call('clear-compiled', []);
			$kernel->call('config:cache', []);

			$form_data['success'] = true;
			$form_data['sql'] = true;
			echo json_encode($form_data);
		}
		catch(Exception $e){
			$logs->write("Usunięcie pliku .env");
			$setup->EnvRemove();
			$form_data['success'] = false;
			$form_data['error'] = $e->getMessage();
			echo json_encode($form_data);
		}
		return;
	}

	$kernel->call('up', []);
	if(pathinfo(getcwd(),PATHINFO_FILENAME) != 'public') chdir('public');
	$auth = SetupHelper::authFromData($_POST ?? [], $_GET ?? []);

	if($auth['autorization']){
		$logs = new Logs(SETUP_LOG_FILE);
		try {
			$setup = new SetupDriver();
			if(!$setup->versionValid($app_version)){
				$logs->write("Zablokowanie dostępu do strony");
				$setup->toggleAccess(false);
				return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Wersja programu ma niepoprawny format</small>"]);
			} else if($setup->versionToInt($setup->env->get('APP_VERSION','0.0.0.0')) > $setup->versionToInt($app_version) && !isset($_GET['downgrade'])){
				$logs->write("Zablokowanie dostępu do strony");
				$setup->toggleAccess(false);
				return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Próbujesz uruchomić starszą wersję eMU niż została już zainicjowana, wykryta v$app_version oczekiwana v".$setup->env->get('APP_VERSION')."</small>"]);
			} else {
				$logs->write("Weryfikacja .env");
				$setup->checkRequired($app_version);
				$logs->write("Pobieranie listy aktualizacji");
				$listaAktualizacji = $setup->CheckForUpdates();
				$logs->write("Odświeżenie cache");
				SetupHelper::refreschCache($kernel,$auth['user'],$auth['type'],$setup->IsCommanderExists(),$setup->IsUserCacheExists());
				if(file_exists('../upgrade-remove-dir.json') || file_exists('../upgrade-remove.json') || file_exists('../upgrade-extract.json')){
					$logs->write("Zablokowanie dostępu do strony");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Proces aktualizacji plików strony jest w toku</small>"]);
				} else if(!file_exists('../guard.ini') && !file_exists('../_Builder')){
					$logs->write("Zablokowanie dostępu do strony");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Nie znaleziono pliku guard.ini</small>"]);
				} else if(!file_exists('../MySQL.ini') && !file_exists('../_Builder')){
					$logs->write("Zablokowanie dostępu do strony");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Nie znaleziono pliku MySQL.ini</small>"]);
				} else if(count($listaAktualizacji) == 0){
					if(!$setup->hasAccess()){
						$logs->write("Odblokowanie dostępu do strony");
						$setup->toggleAccess(true);
					}
					if(file_exists("../auto_update")) unlink("../auto_update");
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Informacja<br><small>Masz już najnowszą aktualizacje</small>"], true);
				} else {
					$logs->write("Zablokowanie dostępu do strony");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallUpdateJqueryForm([], $listaAktualizacji);
				}
			}
		}
		catch(Exception $e){
			return SetupHelper::showInstallUpdateJqueryForm(['error' => $e->getMessage()]);
		}
	} else {
		return SetupHelper::showInstallForm(false, ['errorLogowania' => "Nieprawidłowe hasło"]);
	}
} else {
	try {
		$setup = new SetupDriver();
		if($setup->EnvExists()){
			return SetupHelper::showInstallForm(false);
		} else {
			return SetupHelper::showInstallForm(true);
		}
	}
	catch(Exception $e){
		return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Brak możliwości aktualizacji.<br><small>".$e->getMessage().'</small>']);
	}
}
?>
