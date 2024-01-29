<?php

declare(strict_types=1);

namespace eMU\SDK;

use eMU\Services\Request;

class Core {

	protected string $app_url;
	protected Request $request;
	protected bool $is_logged = false;
	protected int $last_response_code = 0;
	protected array $last_response_data = [];

	public function __construct(string $app_url){
		$this->app_url = "$app_url/public/api";
		$this->request = new Request();
	}

	public function is_logged() : bool {
		return $this->is_logged;
	}

	protected function set_response(array $response) : void {
		$this->last_response_code = $response['code'] ?? 0;
		$this->last_response_data = $response['data'] ?? [];
	}

	public function get_error() : string {
		if($this->last_response_data['error'] ?? false && isset($this->last_response_data['message'])){
			$error = $this->last_response_data['message'] ?? null;
			if(!is_null($error)) return $error;
		}
		return '#'.$this->last_response_code.':'.print_r($this->last_response_data, true);
	}

	public function get_response_code() : int {
		return $this->last_response_code;
	}

	public function get_response_data() : array {
		return $this->last_response_data;
	}

	public function auth(string $token) : void {
		$this->request->setHeader(["Authorization: UserLogonV2 $token"]);
	}

	public function login(string $login, string $password) : bool {
		$this->set_response($this->request->post("$this->app_url/login", ['email' => $login, 'password' => $password, 'role' => 'API.EMU']));
		if($this->get_response_code() != 200) return false;
		$this->request->setHeader(["Authorization: Bearer ".$this->get_response_data()['token']]);
		$this->is_logged = true;
		return true;
	}

	public function logout() : bool {
		$this->set_response($this->request->post("$this->app_url/logout"));
		if($this->get_response_code() != 200) return false;
		$this->request->setHeader([]);
		return true;
	}

	public function get_app_version() : array|false {
		$this->set_response($this->request->get("$this->app_url/get_app_version"));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get_max_upload_file_size() : array|false {
		$this->set_response($this->request->get("$this->app_url/get_max_upload_file_size"));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get_device_id() : string|false {
		$this->set_response($this->request->get("$this->app_url/get_device_id"));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data()['device_id'] ?? false;
	}

	public function get_available_modules(string $format = 'full') : array|false {
		$this->set_response($this->request->get("$this->app_url/get_available_modules", ['format' => $format]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get_status_list(string $status_element_type, int $status_element_final) : array|false {
		$this->set_response($this->request->get("$this->app_url/get_status_list", ['type' => $status_element_type, 'is_final' => $status_element_final]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function getAuth() : array {
		return $this->request->getHeader();
	}
	
}

?>