<?php
	declare(strict_types=1);

	class ExtractorEMU {

		public string $location = '';
		public array $path = [];
		public int $version = 0;

		public array $required_folders = [
			'backup/setup',
			'backup/recovery',
			'public/upgrade',
			'bootstrap/cache',
			'storage/logs',
			'storage/framework/views',
			'storage/framework/testing',
			'storage/framework/sessions',
			'storage/framework/cache',
			'storage/app/public',
			'storage/app/public/temp',
			'storage/app/plugins',
			'storage/app/cache',
			'packages',
			'plugins',
		];

		public function __construct(string $location = __DIR__){
			$this->location = $location;
			$this->path = $this->get_path();
		}

		public function zip_error(int $code) : string {
			switch($code){
				case 0: return "No error";
				case 1: return "Multidisk zip archives are not supported";
				case 2: return "Renaming temporary file failed";
				case 3: return "Failed to close zip archive";
				case 4: return "Seek Error";
				case 5: return "Read error";
				case 6: return "Write error";
				case 7: return "CRC failed";
				case 8: return "The zip archive has been closed";
				case 9: return "No such file";
				case 10: return "File already exists";
				case 11: return "The file cannot be opened";
				case 12: return "Failed to create temporary file";
				case 13: return "Zlib error";
				case 14: return "Disk space reservation error";
				case 15: return "The entry has been changed";
				case 16: return "The compression method is not supported";
				case 17: return "Unexpected EOF";
				case 18: return "Flawed argument";
				case 19: return "This is not a zip archive";
				case 20: return "Internal error";
				case 21: return "ZIP archive is inconsistent";
				case 22: return "The file could not be deleted";
				case 23: return "The entry has been deleted";
				case 2137: return "Insert kremówka";
				default: return "An unknown error has occurred (".intval($code).")";
			}
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

		public function validate() : array {
			$errors = [];

			$PHP_VERSION_NUMBER = $this->get_php_version();
			$extensions = get_loaded_extensions();

			if($PHP_VERSION_NUMBER < 80100 || $PHP_VERSION_NUMBER > 80299) array_push($errors,"Nieprawidłowa wersja PHP wymagana 8.1");

			if($this->parse_size_value(ini_get('memory_limit')) < 128 * 1024 * 1024) array_push($errors,"memory_limit parameter setting required minimum 128M detected ".ini_get('memory_limit'));
			if($this->parse_size_value(ini_get('post_max_size')) < 64 * 1024 * 1024) array_push($errors,"post_max_size parameter setting required minimum 64M detected ".ini_get('post_max_size'));
			if($this->parse_size_value(ini_get('upload_max_filesize')) < 20 * 1024 * 1024) array_push($errors,"upload_max_filesize parameter setting required minimum 20M detected ".ini_get('upload_max_filesize'));
			if(ini_get('max_execution_time') < 180) array_push($errors,"max_execution_time parameter setting required minimum 180 detected ".ini_get('max_execution_time'));

			if(!in_array("tokenizer",$extensions)) array_push($errors,"Missing required extension: Tokenizer");
			if(!in_array("bcmath",$extensions)) array_push($errors,"Missing required extension: BCMath");
			if(!in_array("ctype",$extensions)) array_push($errors,"Missing required extension: Ctype");
			if(!in_array("fileinfo",$extensions)) array_push($errors,"Missing required extension: Fileinfo");
			if(!in_array("curl",$extensions)) array_push($errors,"Missing required extension: cURL");
			if(!in_array("dom",$extensions)) array_push($errors,"Missing required extension: dom");
			if(!in_array("gd",$extensions)) array_push($errors,"Missing required extension: gd");
			if(!in_array("json",$extensions)) array_push($errors,"Missing required extension: JSON");
			if(!in_array("mbstring",$extensions)) array_push($errors,"Missing required extension: Mbstring");
			if(!in_array("openssl",$extensions)) array_push($errors,"Missing required extension: OpenSSL");
			if(!in_array("PDO",$extensions)) array_push($errors,"Missing required extension: PDO");
			if(!in_array("zip",$extensions)) array_push($errors,"Missing required extension: ZipArchive Extension");
			if(!in_array("xml",$extensions)) array_push($errors,"Missing required extension: XML");
			if(!in_array("soap",$extensions)) array_push($errors,"Missing required extension: Soap");
			if(!in_array("imap",$extensions)) array_push($errors,"Missing required extension: IMAP");

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
			if(!$permissions) array_push($errors,"No disk write permissions");

			return $errors;
		}

		public function get_path() : array {
			return [
				'htaccess' => $this->location.'/.htaccess',
				'package' => $this->location.'/data.dog',
				'backup' => [
					'folder' => $this->location.'/backup/setup',
					'zip' => $this->location.'/backup/setup/data.dog',
					'hash' => $this->location.'/backup/setup/data.dog.md5',
					'guard_file' => $this->location.'/backup/setup/guard.ini',
					'guard_driver' => $this->location.'/backup/setup/GuardDriver.php',
					'ini_file' => $this->location.'/backup/setup/IniFile.php',
					'logs_driver' => $this->location.'/backup/setup/Logs.php',
					'version_file' => $this->location.'/backup/setup/version',
				],
			];
		}

		public function package_exists() : bool {
			return file_exists($this->path['package']);
		}

		public function get_package_name() : string {
			return pathinfo($this->path['package'],PATHINFO_BASENAME);
		}

		public function create_folders() : array {
			$errors = [];
			foreach($this->required_folders as $folder){
				if(!file_exists("$this->location/$folder")){
					mkdir("$this->location/$folder",0755,true);
					if(!file_exists("$this->location/$folder")){
						array_push($errors,"$this->location/$folder");
					}
				}
			}
			return $errors;
		}

		public function extract_htaccess() : array {
			$zip = new ZipArchive();
			$res = $zip->open($this->path['package']);
			if($res === TRUE){
				$zip->extractTo($this->location,[
					'public/ht/add_handler80/.htaccess','public/ht/add_handler80/test.php','public/ht/add_type80/.htaccess','public/ht/add_type80/test.php','public/ht/set_env80/.htaccess','public/ht/set_env80/test.php',
					'public/ht/add_handler81/.htaccess','public/ht/add_handler81/test.php','public/ht/add_type81/.htaccess','public/ht/add_type81/test.php','public/ht/set_env81/.htaccess','public/ht/set_env81/test.php',
					'public/ht/add_handler82/.htaccess','public/ht/add_handler82/test.php','public/ht/add_type82/.htaccess','public/ht/add_type82/test.php','public/ht/set_env82/.htaccess','public/ht/set_env82/test.php',
					'public/ht/mod_security/.htaccess',
					'public/ht/mod_security/test.php',
					'.htaccess',
				]);
				$zip->close();

				$htaccess = '';

				$rules = ['mod_security'];
				foreach($rules as $rule){
					if($this->test_htaccess($rule)){
						$htaccess .= $this->get_htaccess_rule($rule)."\r\n\r\n";
					}
				}

				$version = $this->get_php_version();
				if(!($version >= 80100 && $version <= 80299)){
					$platform_check = false;
					$rules = ['add_handler82','add_type82','set_env82'];
					foreach($rules as $rule){
						if($this->test_htaccess($rule)){
							$htaccess .= $this->get_htaccess_rule($rule)."\r\n\r\n";
							if($this->version >= 80200 && $this->version <= 80299){
								$platform_check = true;
							}
						}
					}
					if($platform_check){
						$rules = ['add_handler81','add_type81','set_env81'];
						foreach($rules as $rule){
							if($this->test_htaccess($rule)){
								$htaccess .= $this->get_htaccess_rule($rule)."\r\n\r\n";
								if($this->version >= 80100 && $this->version <= 80299){
									$platform_check = true;
								}
							}
						}
						if(!$platform_check){
							$rules = ['add_handler80','add_type80','set_env80'];
							foreach($rules as $rule){
								if($this->test_htaccess($rule)){
									$htaccess .= $this->get_htaccess_rule($rule)."\r\n\r\n";
								}
							}
						}
					}
				}

				$main_htaccess = "$this->location/.htaccess";
				$htaccess .= file_get_contents($main_htaccess)."\r\n\r\n";

				$custom_htaccess = "$this->location/custom.htaccess";
				if(!file_exists($custom_htaccess)) file_put_contents($custom_htaccess,'');
				$htaccess .= file_get_contents($custom_htaccess)."\r\n\r\n";

				file_put_contents($main_htaccess,$htaccess);
				return ['error' => false, 'message' => 'OK'];
			} else {
				return ['error' => true, 'message' => $this->zip_error(2137)];
			}
		}

		public function extract_installer() : array {
			$zip = new ZipArchive();
			$res = $zip->open($this->path['package']);
			if($res === TRUE){
				$files = [
					$this->path['backup']['guard_file'],
					$this->path['backup']['guard_driver'],
					$this->path['backup']['ini_file'],
					$this->path['backup']['logs_driver'],
					$this->path['backup']['version_file'],
					$this->path['backup']['hash'],
				];
				foreach($files as $file){
					if(file_exists($file)) unlink($file);
				}
				$zip->extractTo($this->location,['public/upgrade/bootstrap.min.css','public/upgrade/driver.php','public/upgrade/jquery-3.6.1.min.js','public/upgrade/upgrade.css','public/upgrade/upgrade.js','public/upgrade.php','public/.htaccess','public/favicon2022.ico']);
				$zip->extractTo($this->path['backup']['folder'],['guard.ini','app/Services/GuardDriver.php','app/Services/IniFile.php','app/Services/Logs.php','version']);
				$zip->close();

				rename($this->path['backup']['folder'].'/app/Services/GuardDriver.php',$this->path['backup']['folder'].'/GuardDriver.php');
				rename($this->path['backup']['folder'].'/app/Services/IniFile.php',$this->path['backup']['folder'].'/IniFile.php');
				rename($this->path['backup']['folder'].'/app/Services/Logs.php',$this->path['backup']['folder'].'/Logs.php');
				rmdir($this->path['backup']['folder'].'/app/Services');
				rmdir($this->path['backup']['folder'].'/app');
				$hash = strtoupper(hash_file("MD5",$this->path['package']));
				rename($this->path['package'],$this->path['backup']['zip']);
				file_put_contents($this->path['backup']['hash'],$hash);
				return ['error' => false, 'message' => 'OK'];
			} else {
				return ['error' => true, 'message' => $this->zip_error(2137)];
			}
		}

		public function get_htaccess_rule(string $template) : string {
			$path = "$this->location/public/ht/$template/.htaccess";
			if(!file_exists($path)) return "";
			return file_get_contents($path);
		}

		public function isSecure() : bool {
			if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'){
				return true;
			} else if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'){
				return true;
			} else if(isset($_SERVER['HTTP_FRONT_END_HTTPS']) && !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'){
				return true;
			} else if(isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT']) === 443){
				return true;
			}
			return false;
		}

		public function test_htaccess(string $template) : bool {
			$url = pathinfo(($this->isSecure() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],PATHINFO_DIRNAME);
			return $this->test_htaccess_request("$url/public/ht/$template/test.php");
		}

		public function test_htaccess_request(string $url) : bool {
			$ch = curl_init($url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			$response = curl_exec($ch);
			if(curl_errno($ch)) return false;
			$http_code = intval(curl_getinfo($ch,CURLINFO_HTTP_CODE));
			curl_close($ch);
			$this->version = intval($response);
			if($http_code == 200 && $this->version >= 80100 && $this->version <= 80299) return true;
			return false;
		}

	}

	$extractor = new ExtractorEMU(__DIR__);
	$validate_host = $extractor->validate();
	if(empty($validate_host)){
		$validate_htaccess = $extractor->extract_htaccess();
	}

?>
<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<title>eMU Extract</title>
		<style>
			body {
				background-color: #F0F2F5;
				--font-family-sans-serif: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
				--font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
				color: #444;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div style="margin-top:15px"></div>
			<div class="row">
				<?php if(!$extractor->package_exists()){ ?>
					<div class="col-xs-12">
						<div class="alert alert-danger" role="alert"><strong>Błąd</strong> File not found <?php echo $extractor->get_package_name(); ?></div>
					</div>
				<?php } else { ?>
					<?php if(isset($validate_htaccess['error']) && $validate_htaccess['error']){ ?>
						<?php echo $validate_htaccess['message']; ?>
					<?php } else { ?>
						<?php if(!empty($validate_host)){ ?>
							<?php foreach($validate_host as $error){ ?>
								<div class="col-xs-12">
									<div class="alert alert-danger" role="alert"><strong>Hosting validation error</strong> <?php echo $error; ?></div>
								</div>
							<?php } ?>
						<?php } else { ?>
							<?php $errors = $extractor->create_folders(); ?>
							<?php if(!empty($errors)){ ?>
								<?php foreach($errors as $error){ ?>
									<div class="col-xs-12">
										<div class="alert alert-danger" role="alert"><strong>Creating folder error</strong> <?php echo $error; ?></div>
									</div>
								<?php } ?>
							<?php } else { ?>
								<?php $installer = $extractor->extract_installer(); ?>
								<?php if($installer['error']){ ?>
									<div class="col-xs-12">
										<div class="alert alert-danger" role="alert"><strong>Installer unpacking error</strong> <?php echo $installer['message']; ?></div>
									</div>
								<?php } else { ?>
									<div class="col-xs-12">
										<div class="alert alert-success" role="alert"><strong>Info</strong> Please wait</div>
									</div>
									<meta http-equiv="refresh" content="5; url=upgrade.php" />
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</body>
