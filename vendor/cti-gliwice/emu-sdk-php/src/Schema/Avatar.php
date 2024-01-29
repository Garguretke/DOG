<?php

declare(strict_types=1);

namespace eMU\Schema;

class Avatar {

	private bool $avatar_used = false;
	private bool $avatar_reset = false;
	private string $avatar_data = '';

	public function __construct(?string $path = null){
		if(!is_null($path)) $this->create_from_file($path);
	}

	public function create_from_file(string $path) : bool {
		if(!file_exists($path)) return false;
		$type = mime_content_type($path);
		$this->avatar_data = "data:$type;base64,".base64_encode(file_get_contents($path));
		$this->avatar_used = true;
		return true;
	}

	public function delete() : void {
		$this->avatar_data = '';
		$this->avatar_used = false;
		$this->avatar_reset = true;
	}

	public function reset() : void {
		$this->avatar_data = '';
		$this->avatar_used = false;
		$this->avatar_reset = false;
	}

	public function getRequest() : array {
		return ['avatar_used' => $this->avatar_used, 'avatar_reset' => $this->avatar_reset, 'avatar_data' => $this->avatar_data];
	}

}

?>
