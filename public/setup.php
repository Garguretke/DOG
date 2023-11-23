<?php

declare(strict_types=1);

const APP_NAME = 'MercjaDOG';
const PHP_REQUIRED = '8.1, 8.2';
const SETUP_LOG_FILE = "../storage/logs/setup.log";

$version_file = "../version";
if(!file_exists($version_file)) die("Failed to read program version: The 'version' file does not exist.");

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
			$logs->write("Start loading migrations: ".$_POST['migracjaDana']['filepath']);
			if(($err = $kernel->call('migrate', ['--path' => str_replace('../', './', $_POST['migracjaDana']['filepath'])])) !== 0){
				$logs->write("Migration error: ".$_POST['migracjaDana']['filepath'].' '.$err);
				throw new Exception('Migration error: ['.$_POST['migracjaDana']['filepath'].'] '.$err);
			}
			$logs->write("Migration loading completed: ".$_POST['migracjaDana']['filepath']);
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
			$logs->write("File integrity verification has been run");
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
		$logs->write("Unblocking access to the website");
		$setup->toggleAccess(true);
		$logs->write(".env verification in progress");
		$setup->checkRequired($app_version);
		$logs->write("Cache refresh in progress");
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

			if(empty($passwordAdmin) || empty($passwordConfirm)) throw new Exception("You must provide a new initial administrator password.");
			if(strcmp($passwordAdmin, $passwordConfirm) !== 0) throw new Exception("The passwords provided are not identical.");
			if(strlen($passwordAdmin) < 6) throw new Exception("The password must be at least 6 characters long.");

			$logs->write("Performing a database connection test.");
			if(!$setup->connectSQL($_POST['DB_HOST'], $_POST['DB_DATABASE'], $_POST['DB_USERNAME'], $_POST['DB_PASSWORD'], intval($_POST['DB_PORT']), $_POST['DB_CONNECTION'])){
				throw new Exception("Database connection error.");
			}

			if($setup->EnvExists()) throw new Exception("The .env configuration file already exists!");

			$logs->write("Creating an .env file");
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

			$logs->write("Initial initiation");
			$kernel->call('config:clear', []);
			$kernel->call('key:generate', []);
			$kernel->call('config:clear', []);
			// $kernel->call('jwt:secret', []);

			$migrationsInstall = [
				'./database/migrations/2014/01'
			];

			foreach($migrationsInstall as $migration){
				$logs->write("Start loading migrations ".$migration);
				if(($err = $kernel->call('migrate', ['--path' => $migration])) !== 0){
					$logs->write("Migration error: ".$migration.' '.$err);
					throw new Exception('Migration error: ['.$migration.'] '.$err);
				}
				$logs->write("Migration loading completed: ".$migration);
			}

			$logs->write("Creating system users");

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
			$logs->write("Deleting the .env file");
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
				$logs->write("Blocking access to the website");
				$setup->toggleAccess(false);
				return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>The program version has an incorrect format.</small>"]);
			} else if($setup->versionToInt($setup->env->get('APP_VERSION','0.0.0.0')) > $setup->versionToInt($app_version) && !isset($_GET['downgrade'])){
				$logs->write("Blocking access to the website");
				$setup->toggleAccess(false);
				return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>You are trying to run an older version of MercjaDOG than has already been initialized, detected v$app_version expected v".$setup->env->get('APP_VERSION').".</small>"]);
			} else {
				$logs->write(".env verification in progress");
				$setup->checkRequired($app_version);
				$logs->write("Downloading the update list");
				$listaAktualizacji = $setup->CheckForUpdates();
				$logs->write("Cache refresh");
				SetupHelper::refreschCache($kernel,$auth['user'],$auth['type'],$setup->IsCommanderExists(),$setup->IsUserCacheExists());
				if(file_exists('../upgrade-remove-dir.json') || file_exists('../upgrade-remove.json') || file_exists('../upgrade-extract.json')){
					$logs->write("Blocking access to the website");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>The process of updating site files is in progress.</small>"]);
				} else if(!file_exists('../guard.ini') && !file_exists('../_Builder')){
					$logs->write("Blocking access to the website");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>guard.ini file not found.</small>"]);
				} else if(!file_exists('../MySQL.ini') && !file_exists('../_Builder')){
					$logs->write("Blocking access to the website");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>MySQL.ini file not found.</small>"]);
				} else if(count($listaAktualizacji) == 0){
					if(!$setup->hasAccess()){
						$logs->write("Unblocking access to the website");
						$setup->toggleAccess(true);
					}
					if(file_exists("../auto_update")) unlink("../auto_update");
					return SetupHelper::showInstallForm(false, ['errorGlobalny' => "Info<br><small>You already have the latest update.</small>"], true);
				} else {
					$logs->write("Blocking access to the website");
					$setup->toggleAccess(false);
					return SetupHelper::showInstallUpdateJqueryForm([], $listaAktualizacji);
				}
			}
		}
		catch(Exception $e){
			return SetupHelper::showInstallUpdateJqueryForm(['error' => $e->getMessage()]);
		}
	} else {
		return SetupHelper::showInstallForm(false, ['errorLogowania' => "Invalid password"]);
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
		return SetupHelper::showInstallForm(false, ['errorGlobalny' => "No update possible.<br><small>".$e->getMessage().'</small>']);
	}
}
?>
