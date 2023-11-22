<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class SetupDriver {

	private Capsule $sql;
	private string $env_path;

	public IniFile $env;
	// public AppBuffer $app_buffer;

	public function __construct(){
		try {
			if(pathinfo(getcwd(),PATHINFO_FILENAME) == 'public'){
				$this->env_path = '../.env';
			} else {
				$this->env_path = '.env';
			}
			if($this->EnvExists()){
				$this->env = new IniFile($this->env_path, true);
				$this->sql = $this->connectSQL($this->env->get('DB_HOST'),$this->env->getString('DB_DATABASE'),$this->env->getString('DB_USERNAME'),$this->env->getString('DB_PASSWORD'),$this->env->get('DB_PORT'),$this->env->get('DB_CONNECTION'));
			}
			// $this->app_buffer = new AppBuffer();
		}
		catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function connectSQL(string $host, string $database, string $username, string $password, int $port, string $driver) : Capsule {
		try {
			$sqlCN = new Capsule();
			$sqlCN->addConnection([
				'driver' => $driver,
				'host' => $host,
				'database' => $database,
				'username' => $username,
				'password' => $password,
				'port' => $port,
				'charset' => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix' => ''
			]);
			$sqlCN->setAsGlobal();
			$sqlCN->bootEloquent();
			$sqlCN->connection()->getPdo();
			return $sqlCN;
		}
		catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function toggleAccess(bool $toggle) : void {
		// $app_buffer = new AppBuffer();
		// $app_buffer->set('WEBSITE_LOCKED_BY_SETUP', !$toggle);
	}

	public function hasAccess() : bool {
		return true;
		// $app_buffer = new AppBuffer();
		// return !$app_buffer->get('WEBSITE_LOCKED_BY_SETUP', false);
	}

	public function versionValid(string $version) : bool {
		$ver = explode(".",$version);
		$ver[1] = intval($ver[1] ?? -1);
		$ver[2] = intval($ver[2] ?? -1);
		$ver[3] = intval($ver[3] ?? -1);
		if($ver[1] > 99 || $ver[2] > 99 || $ver[3] > 99 || $ver[1] < 0 || $ver[2] < 0 || $ver[3] < 0) return false;
		return true;
	}

	public function versionToInt(string $version) : int {
		$ver = explode(".",$version);
		return intval($ver[0])*1000000 + intval($ver[1])*10000 + intval($ver[2])*100 + intval($ver[3]);
	}

	public function fetchPlugin(string $device_id, string $app_version, string $key, string $code) : void {
		$logs = new Logs("../storage/logs/setup.log");
		$logs->write("Pobieranie plugina: $code");
		$data = json_encode(['device_id' => $device_id, 'type' => 'PLUGIN', 'version' => $app_version, 'name' => $key]);
		$ch = curl_init(config('app.slug_server').'/public/api/get-download-links');
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json', 'Content-Length: '.strlen($data)]);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response ?? '[]',true);
		if(!($response['error'] ?? true)){
			$link = json_decode($response['links'],true);
			if(isset($link[0]['url'])) $this->downloadPlugin($link[0]['url'],$link[0]['name']);
		}
	}

	public function downloadPlugin(string $url, string $name) : void {
		$ch = curl_init($url);
		$file_name = basename($name);
		if(file_exists("../plugins/$file_name")){
			unlink("../plugins/$file_name");
		}
		$fp = fopen("../plugins/$file_name",'wb');
		if($fp){
			curl_setopt($ch,CURLOPT_FILE,$fp);
			curl_setopt($ch,CURLOPT_HEADER,0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
		}
	}

	public function checkRequired(string $app_version) : void {
		$changed = false;

		if(is_null($this->env->get('APP_DEVICE_ID'))){
			$this->env->set('APP_DEVICE_ID', strtoupper(hash('sha256', date('Y-m-d H:i:s').uniqid())));
			$changed = true;
		}

		if($this->env->get('APP_VERSION') != $app_version && $app_version != '0.0.0.0'){
			$version_history = $this->env->get('HISTORY_VERSION',[]);
			if(empty($version_history) && !is_null($this->env->get('APP_VERSION'))){
				array_push($version_history,$this->env->get('APP_VERSION'));
			}
			if(!in_array($app_version,$version_history)){
				array_push($version_history,$app_version);
			}
			$this->env->set('HISTORY_VERSION', $version_history);
			$this->env->set('APP_VERSION', $app_version);
			$changed = true;

		}

		if(is_null($this->env->get('APP_KEY'))){
			$this->env->set('APP_KEY', 'base64:'.base64_encode(Encrypter::generateKey('AES-256-CBC')));
			$changed = true;
		}
		if(empty($this->env->get('JWT_SECRET'))){
			$this->env->set('JWT_SECRET', Str::random(64));
			$changed = true;
		}
		if($this->env->get('APP_ENV') != 'local'){
			$this->env->set('APP_ENV','local');
			$changed = true;
		}
		if($changed){
			$this->env->save();
		}
	}

	public function EnvCreate(array $data) : void {
		try {
			$this->env = new IniFile($this->env_path, true);
			if(!$this->env->isValid()) throw new Exception("Tworzenie pliku .env nie powiodło się");
			$this->env->update([
				'APP_ENV' => "local",
				'APP_DEBUG' => false,
				'APP_KEY' => 'base64:'.base64_encode(Encrypter::generateKey('AES-256-CBC')),
				'APP_URL' => $data['app_url'],
				'APP_LOG_LEVEL' => "info",
				'DB_CONNECTION' => $data['db_connection'],
				'DB_HOST' => $data['db_hostname'],
				'DB_PORT' => $data['db_port'],
				'DB_DATABASE' => $data['db_database'],
				'DB_USERNAME' => $data['db_username'],
				'DB_PASSWORD' => $data['db_password'],
				'MAIL_DRIVER' => "smtp",
				'MAIL_HOST' => "",
				'MAIL_PORT' => 587,
				'MAIL_USERNAME' => "",
				'MAIL_PASSWORD' => "",
				'MAIL_ENCRYPTION' => "tls",
				'MAIL_COPY_MAIL' => "",
				'MAIL_FROM_ADDRESS' => $data['email'],
				'MAIL_FROM_NAME' => "eMU",
				'MAIL_SEND_CC' => 0,
				'APP_FORCE_SSL' => false,
				'SESSION_LIFETIME' => 60,
				'SESSION_DOMAIN' => $data['session_domain'] ?? null,
			], true);
		}
		catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function EnvExists() : bool {
		if(file_exists($this->env_path)) return true;
		return false;
	}

	public function EnvRemove() : void {
		if(file_exists($this->env_path)) unlink($this->env_path);
	}

	public function checkValidateFiles() : void {
		if(pathinfo(getcwd(),PATHINFO_BASENAME) == 'public'){
			chdir('..');
			$changed_dir = true;
		} else {
			$changed_dir = false;
		}
		$guard = new GuardDriver();
		$validation = $guard->validate();
		$damaged = 0;
		$missing = 0;
		$unknown = 0;
		$errors = [
			'damaged' => [],
			'unknown' => [],
			'missing' => [],
		];
		$auto_recovery = [];
		foreach($validation as $error){
			switch($error['type']){
				case 'damaged': {
					$damaged++;
					array_push($errors['damaged'],$error['file']);
					array_push($auto_recovery,$error['file']);
					break;
				}
				case 'unknown': {
					$unknown++;
					array_push($errors['unknown'],$error['file']);
					break;
				}
				case 'missing': {
					$missing++;
					array_push($errors['missing'],$error['file']);
					array_push($auto_recovery,$error['file']);
					break;
				}
			}
		}

		$msg = "Walidacja plików strony: $damaged uszkodzonych, $missing brakujących, $unknown nierozpoznanych";
		logger()->error("SetupDriver -> ".$msg);

		if(!empty($validation)){
			$file_name = 'validator-'.date("Y-m-d_His").'.log';
			$logs = new Logs("storage/logs/$file_name",false);
			$logs->write(date("Y-m-d H:i:s")." Weryfikacja spójności plików");
			$logs->write("Status: $damaged uszkodzonych, $missing brakujących, $unknown nierozpoznanych");
			$logs->write("\r\nUszkodzone:");
			$logs->write($errors['damaged'] ?? []);
			$logs->write("\r\nBrakujące:");
			$logs->write($errors['missing'] ?? []);
			$logs->write("\r\nNierozpoznane:");
			$logs->write($errors['unknown'] ?? []);
			$logs->write("\r\n\r\n");
		}
		$count_validator = 0;
		$count_upgrade = 0;
		if(!file_exists('storage/logs')) mkdir('storage/logs',0755,true);
		$files = scandir('storage/logs',SCANDIR_SORT_DESCENDING);
		foreach($files as $file){
			if(substr(pathinfo($file,PATHINFO_FILENAME),0,10) == 'validator-'){
				if($count_validator >= 7) unlink("storage/logs/$file");
				$count_validator++;
			} else if(substr(pathinfo($file,PATHINFO_FILENAME),0,8) == 'upgrade-'){
				if($count_upgrade >= 7) unlink("storage/logs/$file");
				$count_upgrade++;
			}
		}
		if(!empty($auto_recovery) && !file_exists('_Builder')){
			if(file_exists('auto-recovery.json')) unlink('auto-recovery.json');
			file_put_contents('auto-recovery.json', json_encode($auto_recovery));
			$this->env->update(['SYSTEM_AUTO_RECOVERY' => true], true);
		}
		if($changed_dir) chdir('public');
	}

	public function CheckForUpdates() : array {
		try {
			$installed_migrations = $this->sql->table('migrations')->pluck('migration')->toArray();
		}
		catch(Exception $e){
			return [];
		}
		$data = [];
		$path = "../database/migrations";
		$years = scandir($path,SCANDIR_SORT_ASCENDING);
		foreach($years as $year){
			if($year == '.' || $year == '..' || !is_dir("$path/$year")) continue;
			$months = scandir("$path/$year",SCANDIR_SORT_ASCENDING);
			foreach($months as $month){
				if($month == '.' || $month == '..' || !is_dir("$path/$year/$month")) continue;
				$migrations = scandir("$path/$year/$month",SCANDIR_SORT_ASCENDING);
				foreach($migrations as $migration){
					if(pathinfo("$path/$year/$month/$migration",PATHINFO_EXTENSION) == 'php'){
						$name = pathinfo($migration,PATHINFO_FILENAME);
						if(!in_array($name, $installed_migrations)){
							array_push($data,['filename' => "$migration", 'migrationName' => $name, 'filepath' => "$path/$year/$month/$migration"]);
						}
					}
				}
			}
		}
		return $data;
	}

	public function IsTableExists(string $table) : bool {
		$schema = $this->sql->schema();
		return $schema->hasTable($table);
	}

	public function IsCommanderExists() : bool {
		return $this->IsTableExists('commander');
	}

	public function IsSettingsExists() : bool {
		return $this->IsTableExists('settings');
	}

	public function IsUserCacheExists() : bool {
		return $this->IsTableExists('user_caches');
	}

}

?>
