<?php
	use App\Services\GuardDriver as GuardDriver;
	use App\Services\Logs as Logs;

	chdir(__DIR__.'/../..');
	$path = [
		'version' => 'backup/setup/version',
		'guard' => 'backup/setup/guard.ini',
		'pack' => 'backup/setup/data.dog',
		'hash' => 'backup/setup/data.dog.md5',
		'ini_file' => 'backup/setup/IniFile.php',
		'logs_driver' => 'backup/setup/Logs.php',
		'guard_driver' => 'backup/setup/GuardDriver.php',
	];

	$file_upgrade_remove = 'upgrade-remove.json';
	$file_upgrade_remove_dir = 'upgrade-remove-dir.json';
	$file_upgrade_extract = 'upgrade-extract.json';

	$init_errors = [];
	foreach($path as $p) if(!file_exists($p)){
		array_push($init_errors,"Plik $p nie istnieje.");
	}

	$action = $_POST['action'] ?? '';
	if(!empty($init_errors)){
		echo json_encode(['error' => true, 'messages' => $init_errors]);
	} else {
		require_once($path['ini_file']);
		require_once($path['guard_driver']);
		require_once($path['logs_driver']);

		$file_name = 'upgrade-'.date("Y-m-d").'.log';
		$logs = new Logs("storage/logs/$file_name",false);

		if($action == 'lock_website'){
			if(!file_exists('auto_update')){
				file_put_contents('auto_update','auto_update');
			}
			$files = ['server.php','is_beta','bootstrap/cache/config.php','bootstrap/cache/packages.php','bootstrap/cache/routes.php','bootstrap/cache/services.php','bootstrap/cache/routes-v7.php','auto-recovery.json'];
			foreach($files as $file){
				if(file_exists($file)) unlink($file);
			}
			echo json_encode(['error' => false]);
		} else if($action == 'init_extract'){
			$logs->write('Skanowanie plików strony');
			if(file_exists($file_upgrade_remove)) unlink($file_upgrade_remove);
			if(file_exists($file_upgrade_remove_dir)) unlink($file_upgrade_remove_dir);
			if(file_exists($file_upgrade_extract)) unlink($file_upgrade_extract);
			$hash = strtoupper(hash_file("MD5",$path['pack']));
			if(strlen($hash) == 32 && substr($hash,0,32) == substr(file_get_contents($path['hash']),0,32)){
				$guard = new GuardDriver($path['guard']);
				$errors = $guard->validate();
				$files_extract = ['guard.ini'];
				$files_remove = [];
				$upgrade_remove_dir = $guard->getUnusedFolders();
				foreach($errors as $error){
					if($error['type'] == 'unknown'){
						array_push($files_remove, $error['file']);
					} else {
						array_push($files_extract, $error['file']);
					}
				}
				array_push($files_extract,'server.php');
				file_put_contents($file_upgrade_remove_dir, json_encode($upgrade_remove_dir));
				file_put_contents($file_upgrade_remove, json_encode($files_remove));
				file_put_contents($file_upgrade_extract, json_encode($files_extract));
				echo json_encode([
					'error' => false,
					'files_remove' => count($files_remove),
					'files_extract' => count($files_extract),
					'folders_remove' => count($upgrade_remove_dir),
					'auto_update' => file_exists('.env') ? 1 : 0,
				]);
			} else {
				echo json_encode(['error' => true, 'messages' => ['Plik data.dog jest uszkodzony.']]);
			}
		} else if($action == 'remove_unused'){
			if(file_exists($file_upgrade_remove)){
				$files = json_decode(file_get_contents($file_upgrade_remove), true);
				if(gettype($files) == 'array'){
					$log = [];
					$date = date('Y-m-d_His');
					foreach($files as $file){
						if(file_exists($file)){
							array_push($log,"Usunięcie pliku: $file");
							$fdir = pathinfo($file,PATHINFO_DIRNAME);
							if(!file_exists("_deleted/$date/$fdir")) mkdir("_deleted/$date/$fdir",0755,true);
							rename($file,"_deleted/$date/$file");
						}
					}
					$logs->write($log);
					unlink($file_upgrade_remove);
					echo json_encode(['error' => false]);
				} else {
					echo json_encode(['error' => true, 'messages' => ["Błąd odczytu $file_upgrade_remove"]]);
				}
			} else {
				echo json_encode(['error' => true, 'messages' => ["Nie znaleziono pliku $file_upgrade_remove"]]);
			}
		} else if($action == 'remove_unused_dir'){
			if(file_exists($file_upgrade_remove_dir)){
				$files = json_decode(file_get_contents($file_upgrade_remove_dir), true);
				if(gettype($files) == 'array'){
					$log = [];
					foreach($files as $file){
						if(file_exists($file)){
							try {
								rmdir($file);
								if(!file_exists($file)) array_push($log,"Usunięcie folderu: $file");
							}
							catch(Exception $e){

							}
						}
					}
					$logs->write($log);
					unlink($file_upgrade_remove_dir);
					echo json_encode(['error' => false]);
				} else {
					echo json_encode(['error' => true, 'messages' => ["Błąd odczytu $file_upgrade_remove_dir"]]);
				}
			} else {
				echo json_encode(['error' => true, 'messages' => ["Nie znaleziono pliku $file_upgrade_remove_dir"]]);
			}
		} else if($action == 'extract'){
			if(file_exists($file_upgrade_extract)){
				$files = json_decode(file_get_contents($file_upgrade_extract), true);
				if(gettype($files) == 'array'){
					$zip = new ZipArchive();
					$res = $zip->open($path['pack']);
					if($res === TRUE){
						$zip->extractTo(getcwd(),$files);
						if($zip->locateName('is_beta') !== false){
							$zip->extractTo(getcwd(),'is_beta');
						}
						$zip->close();
						$log = [];
						foreach($files as $file){
							array_push($log,"Rozpakowanie pliku: $file");
						}
						array_push($log,'');
						$logs->write($log);
						unlink($file_upgrade_extract);
						if(!file_exists('backup/recovery')){
							mkdir('backup/recovery',0755,true);
						} else {
							if(file_exists('backup/recovery/version')) unlink('backup/recovery/version');
							if(file_exists('backup/recovery/guard.ini')) unlink('backup/recovery/guard.ini');
							if(file_exists('backup/recovery/data.dog')) unlink('backup/recovery/data.dog');
							if(file_exists('backup/recovery/data.dog.md5')) unlink('backup/recovery/data.dog.md5');
						}
						rename($path['version'],'backup/recovery/version');
						rename($path['guard'],'backup/recovery/guard.ini');
						rename($path['pack'],'backup/recovery/data.dog');
						rename($path['hash'],'backup/recovery/data.dog.md5');
						if(function_exists('opcache_reset')) @opcache_reset();
						$files = ['bootstrap/cache/config.php','bootstrap/cache/packages.php','bootstrap/cache/routes.php','bootstrap/cache/services.php','bootstrap/cache/routes-v7.php'];
						foreach($files as $file){
							if(file_exists($file)) unlink($file);
						}
						echo json_encode(['error' => false]);
					} else {
						echo json_encode(['error' => true, 'messages' => ['Błąd wypakowywania zip: Kod #'.$res]]);
					}
				} else {
					echo json_encode(['error' => true, 'messages' => ["Błąd odczytu $file_upgrade_extract"]]);
				}
			} else {
				echo json_encode(['error' => true, 'messages' => ["Nie znaleziono pliku $file_upgrade_extract"]]);
			}
		} else {
			echo json_encode(['error' => true, 'messages' => ['Nieznana akcja.']]);
		}
	}
?>
