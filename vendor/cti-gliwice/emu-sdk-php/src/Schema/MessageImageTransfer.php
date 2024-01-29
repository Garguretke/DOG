<?php

declare(strict_types=1);

namespace eMU\Schema;

class MessageImageTransfer {

	protected array $files = [];

	public function add(string $path) : bool {
		if(!file_exists($path)) return false;
		$type = mime_content_type($path);
		array_push($this->files, "data:$type;base64,".base64_encode(file_get_contents($path)));
		return true;
	}

	public function reset() : void {
		$this->files = [];
	}

	public function getRequest() : array {
		return $this->files;
	}

}

?>
