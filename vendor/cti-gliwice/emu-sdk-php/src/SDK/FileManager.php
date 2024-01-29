<?php

declare(strict_types=1);

namespace eMU\SDK;

class FileManager extends Core {

	protected string $api_url;

	public function __construct(string $app_url, ?array $auth = null){
		parent::__construct($app_url);
		if(!is_null($auth)) $this->request->setHeader($auth);
		$this->api_url = "$this->app_url/emu/filemanager";
	}

	public function delete(string $path) : array|false {
		$this->set_response($this->request->post("$this->api_url/delete", ['path' => $path]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function create_folder(string $path, string $name, bool|null $inherited = null, bool $shared = false, ?string $shared_name = null, ?string $valid_from = null, ?string $valid_until = null, array $permissions = []) : array|false {
		$data = ['path' => $path, 'name' => $name];
		if(!is_null($inherited)) $data['inherited'] = $inherited;
		if($shared){
			$data['shared'] = true;
			$data['shared_name'] = $shared_name;
		}
		if(!is_null($valid_from)) $data['valid_from'] = $valid_from;
		if(!is_null($valid_until)) $data['valid_until'] = $valid_until;
		if(!empty($permissions)) $data['permissions'] = $permissions;
		$this->set_response($this->request->post("$this->api_url/create_folder", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function download_file(string $path, string $file) : bool {
		$file_info = $this->get_file_info($path);
		if(!$file_info) return false;
		$content = file_get_contents($file_info['url']);
		if(!$content) return false;
		file_put_contents($file, $content);
		return true;
	}

	public function get_file_blob(string $path) : string|false {
		$file_info = $this->get_file_info($path);
		if(!$file_info) return false;
		return file_get_contents($file_info['url']);
	}
	
	public function create_file(string $path, string $name, string $content, string $content_type, bool $shared = false, ?string $shared_name = null, ?string $valid_from = null, ?string $valid_until = null, array $permissions = []) : array|false {
		$data = ['path' => $path, 'name' => $name, 'content' => $content, 'content_type' => $content_type];
		if($shared){
			$data['shared'] = true;
			$data['shared_name'] = $shared_name;
		}
		if(!is_null($valid_from)) $data['valid_from'] = $valid_from;
		if(!is_null($valid_until)) $data['valid_until'] = $valid_until;
		if(!empty($permissions)) $data['permissions'] = $permissions;
		$this->set_response($this->request->post("$this->api_url/create_file", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}
	public function send_file(string $path, string $name, string $file, bool $shared = false, ?string $shared_name = null, ?string $valid_from = null, ?string $valid_until = null, array $permissions = []) : array|false {
		$data = ['path' => $path, 'name' => $name, 'content' => base64_encode(file_get_contents($file)), 'content_type' => 'base64'];
		if($shared){
			$data['shared'] = true;
			$data['shared_name'] = $shared_name;
		}
		if(!is_null($valid_from)) $data['valid_from'] = $valid_from;
		if(!is_null($valid_until)) $data['valid_until'] = $valid_until;
		if(!empty($permissions)) $data['permissions'] = $permissions;
		$this->set_response($this->request->post("$this->api_url/create_file", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function exists(string $path) : bool {
		$this->set_response($this->request->post("$this->api_url/exists", ['path' => $path]));
		if($this->get_response_code() != 200) return false;
		return ($this->get_response_data()['exists'] ?? 0) == 1;
	}

	public function get_file_info(string $path) : array|false {
		$this->set_response($this->request->post("$this->api_url/get_file_info", ['path' => $path]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}
	
	public function get_folder_items(string $path) : array|false {
		$this->set_response($this->request->post("$this->api_url/get_folder_items", ['path' => $path]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get_tree() : array|false {
		$this->set_response($this->request->post("$this->api_url/get_tree"));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function share(string $path, bool $shared = false, ?string $shared_name = null, ?string $valid_from = null, ?string $valid_until = null) : array|false {
		$data = ['path' => $path, 'shared' => $shared];
		if($shared) $data['shared_name'] = $shared_name;
		if(!is_null($valid_from)) $data['valid_from'] = $valid_from;
		if(!is_null($valid_until)) $data['valid_until'] = $valid_until;
		$this->set_response($this->request->post("$this->api_url/share", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function get_permissions(string $path) : array|false {
		$this->set_response($this->request->post("$this->api_url/get_permissions", ['path' => $path]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function set_permissions(string $path, bool $inherited, array $permissions = []) : array|false {
		$data = ['path' => $path, 'inherited' => $inherited];
		if(!empty($permissions)) $data['permissions'] = $permissions;
		$this->set_response($this->request->post("$this->api_url/set_permissions", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function set_owner(string $path, ?int $owner_id = null) : array|false {
		$this->set_response($this->request->post("$this->api_url/set_owner", ['path' => $path, 'owner_id' => $owner_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function move(string $source, string $destination) : array|false {
		$this->set_response($this->request->post("$this->api_url/move", ['source' => $source, 'destination' => $destination]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function copy(string $source, string $destination, bool $with_permissions = true) : array|false {
		$this->set_response($this->request->post("$this->api_url/copy", ['source' => $source, 'destination' => $destination, 'with_permissions' => $with_permissions]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function copy_folder_structure(string $source, string $destination, bool $with_permissions = true) : array|false {
		$this->set_response($this->request->post("$this->api_url/copy_folder_structure", ['source' => $source, 'destination' => $destination, 'with_permissions' => $with_permissions]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}
	
}

?>