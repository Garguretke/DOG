<?php

declare(strict_types=1);

namespace eMU\SDK;

use Exception;
use eMU\Traits\ElementEditor;

class Order extends Core {

	use ElementEditor;
	
	protected string $api_url;

	public function __construct(string $app_url, ?array $auth = null){
		parent::__construct($app_url);
		if(!is_null($auth)) $this->request->setHeader($auth);
		$this->api_url = "$this->app_url/emu/order";
	}

	public function open(int $id) : array|false {
		$this->set_response($this->request->post("$this->api_url/open", ['id' => $id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	public function close(int $id) : array|false {
		$this->set_response($this->request->post("$this->api_url/close", ['id' => $id]));
		if($this->get_response_code() != 200) return false;
		return $this->get_response_data();
	}

	/**
	 * @deprecated "Operation delete is not supported for this element type"
	 *
	 * @return $this
	 */
	public function delete(int $id) : array|false {
		throw new Exception("Operation ".static::class."::delete is not supported for this element type");
	}

	/**
	 * @deprecated "Operation restore is not supported for this element type"
	 *
	 * @return $this
	 */
	public function restore(int $id) : array|false {
		throw new Exception("Operation ".static::class."::restore is not supported for this element type");
	}

}

?>