<?php

declare(strict_types=1);

namespace App\Tools;

use AVE;

class LaravelProject {

	private string $name = "LaravelProject";

	private string $php_version = "8.2";
	private string $app_version = "0.0.0.0";
	private string $temp_folder;

	private array $params = [];
	private string $path;
	private string $php_path;
	private AVE $ave;

	public function __construct(AVE $ave){
		$this->ave = $ave;
		$this->ave->set_tool($this->name);
		$this->php_path = $this->ave->get_file_path($this->ave->get_variable('%PROGRAMFILES%')."/AVE-UTILITIES/php");
	}

	public function compile_plugin(string $name, string $destination) : void {
		if(!file_exists($destination)) $this->ave->mkdir($destination);
		$this->php($this->ave->get_file_path("$this->path/plugins_src/compiler.php")." \"$name\" \"$this->app_version\"", false);
		$input = $this->ave->get_file_path("$this->path/plugins/$name.emu-plugin");
		$output = $this->ave->get_file_path("$destination/$name.emu-plugin");
		if(file_exists($output)) $this->ave->unlink($output);
		if(file_exists($input)) $this->ave->rename($input, $output);
	}

	public function artisan(string $command, bool $info = true) : void {
		if($info) $this->ave->echo(" Executing a command artisan $command");
		$php = $this->ave->get_file_path($this->php_path."/".$this->php_version."/php.exe");
		system("\"$php\" \"artisan\" \"$command\"");
	}

	public function php(string $command, bool $info = true) : void {
		if($info) $this->ave->echo(" Executing a command php $command");
		$php = $this->ave->get_file_path($this->php_path."/".$this->php_version."/php.exe");
		system("\"$php\" $command");
	}

	public function set_php_version(string $version) : bool {
		if(!file_exists($this->ave->get_file_path($this->php_path."/$version"))){
			$this->ave->echo(" The required PHP v$version was not found in AVE-UTILITIES");
			return false;
		} else {
			$this->php_version = "$version";
			return true;
		}
	}

	public function build(string $path) : bool {
		$script = $this->ave->get_file_path("$path/WebBuilder/builder.php");
		if(!file_exists($script)){
			$this->ave->echo(" File not found $script");
			return true;
		}
		$this->path = $path;
		unset($path);
		chdir($this->path);
		$this->temp_folder = $this->ave->get_file_path($this->ave->get_variable("%TMP%")."/web-builder.".uniqid());
		if(file_exists("version")) $this->app_version = preg_replace('/\s+/','', file_get_contents("version"));
		if(file_exists($this->temp_folder)) $this->ave->rrmdir($this->temp_folder);
		$this->ave->mkdir($this->temp_folder);
		eval(str_replace(["?>", "<?php", "<?"], "", file_get_contents($script)));
		$this->ave->rrmdir($this->temp_folder);
		chdir($this->ave->path);
		return true;
	}

}

?>
