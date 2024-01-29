<?php

declare(strict_types=1);

namespace eMU\SDK;

use eMU\Schema\Avatar;
use eMU\Schema\DataTransfer;

class EmJuJa extends Core {

	protected string $api_url;

	public function __construct(string $app_url, ?array $auth = null){
		parent::__construct($app_url);
		if(!is_null($auth)) $this->request->setHeader($auth);
		$this->api_url = "$this->app_url/emu/emjuja";
	}

	public function user_get_messages(int $user_id, int $last_message = 0, bool $fetch_all = false, bool $mark_as_read = false) : array|false {
		$this->set_response($this->request->post("$this->api_url/user/get_messages", ['user_id' => $user_id, 'last_message' => $last_message, 'fetch_all' => $fetch_all, 'mark_as_read' => $mark_as_read]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function user_send_message(int $user_id, string $message, ?DataTransfer $files = null) : array|false {
		$data = ['user_id' => $user_id, 'message' => $message];
		if(!is_null($files)) $data = array_merge($data, $files->getRequest());
		$this->set_response($this->request->post("$this->api_url/user/send_message", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function user_delete_message(int|array $message_id) : array|false {
		$this->set_response($this->request->post("$this->api_url/user/delete_message", ['message_id' => $message_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function user_delete_conversation(int|array $user_id, ?string $date_from = null, ?string $date_until = null) : array|false {
		$this->set_response($this->request->post("$this->api_url/user/delete_conversation", ['user_id' => $user_id, 'date_from' => $date_from, 'date_until' => $date_until]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_get_messages(int $group_id, int $last_message = 0, bool $fetch_all = false, bool $mark_as_read = false) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/get_messages", ['group_id' => $group_id, 'last_message' => $last_message, 'fetch_all' => $fetch_all, 'mark_as_read' => $mark_as_read]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_send_message(int $group_id, string $message, ?DataTransfer $files = null) : array|false {
		$data = ['group_id' => $group_id, 'message' => $message];
		if(!is_null($files)) $data = array_merge($data, $files->getRequest());
		$this->set_response($this->request->post("$this->api_url/group/send_message", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_delete_message(int|array $message_id) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/delete_message", ['message_id' => $message_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_delete_conversation(int|array $group_id, ?string $date_from = null, ?string $date_until = null) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/delete_conversation", ['group_id' => $group_id, 'date_from' => $date_from, 'date_until' => $date_until]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_list(?string $search = null, bool $only_owned = false) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/list", ['search' => $search, 'only_owned' => $only_owned]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_get(int $group_id) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/get", ['group_id' => $group_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_create(string $name, string $color, array $users_in_group, bool $restricted = false, ?Avatar $avatar = null) : array|false {
		$data = ['name' => $name, 'color' => $color, 'users_in_group' => $users_in_group, 'restricted' => $restricted];
		if(!is_null($avatar)) $data = array_merge($data, $avatar->getRequest());
		$this->set_response($this->request->post("$this->api_url/group/editor", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_edit(int $id, string $name, string $color, array $users_in_group, bool $restricted = false, ?Avatar $avatar = null) : array|false {
		$data = ['id' => $id, 'name' => $name, 'color' => $color, 'users_in_group' => $users_in_group, 'restricted' => $restricted];
		if(!is_null($avatar)) $data = array_merge($data, $avatar->getRequest());
		$this->set_response($this->request->post("$this->api_url/group/editor", $data));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_delete(int $group_id) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/delete", ['group_id' => $group_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function group_leave(int $group_id) : array|false {
		$this->set_response($this->request->post("$this->api_url/group/leave", ['group_id' => $group_id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

}

?>