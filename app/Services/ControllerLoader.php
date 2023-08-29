<?php

declare(strict_types=1);

namespace App\Services;

class ControllerLoader {

	private string $path;
	private array $except;

	public function __construct(string $path){
		$this->path = $path;
	}

	public function setExcept(array $except = []) : void {
		$this->except = $except;
	}

	public function load() : void {
		$files = scandir("$this->path");
		foreach($files as $file){
			$name = pathinfo($file, PATHINFO_FILENAME);
			if(pathinfo($file, PATHINFO_EXTENSION) == 'php' && !in_array($name, $this->except)){
				$class = "\\App\Http\\Controllers\\$name";
				if(method_exists($class, 'routes')){
					$class::routes();
				}
			}
		}
	}

}
