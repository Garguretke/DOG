<?php

declare(strict_types=1);

namespace eMU\SDK;

class UserNote extends Core {

	protected string $api_url;

	public function __construct(string $app_url, ?array $auth = null){
		parent::__construct($app_url);
		if(!is_null($auth)) $this->request->setHeader($auth);
		$this->api_url = "$this->app_url/emu/usernote";
	}

	public function list(?string $search = null, bool $with_trashed = false, bool $with_description = false) : array|false {
		$this->set_response($this->request->post("$this->api_url/list", ['search' => $search, 'with_trashed' => $with_trashed, 'with_description' => $with_description]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get(int $id) : array|false {
		$this->set_response($this->request->post("$this->api_url/get", ['id' => $id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function create(?string $title, ?string $description, ?string $color = null, ?string $font_color = null, ?int $order = null) : array|false {
		$this->set_response($this->request->post("$this->api_url/create", ['title' => $title, 'description' => $description, 'color' => $color, 'font_color' => $font_color, 'order' => $order]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function edit(int $id, ?string $title, ?string $description, ?string $color = null, ?string $font_color = null, ?int $order = null) : array|false {
		$this->set_response($this->request->post("$this->api_url/edit", ['id' => $id, 'title' => $title, 'description' => $description, 'color' => $color, 'font_color' => $font_color, 'order' => $order]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function delete(int $id, bool $permanent = false) : array|false {
		$this->set_response($this->request->post("$this->api_url/delete", ['id' => $id, 'permanent' => $permanent]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function restore(int $id) : array|false {
		$this->set_response($this->request->post("$this->api_url/restore", ['id' => $id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

}

?>