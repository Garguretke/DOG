<?php

declare(strict_types=1);

namespace eMU\SDK;

use eMU\Traits\ElementEditor;

class TradeTask extends Core {

	use ElementEditor;
	
	protected string $api_url;

	public function __construct(string $app_url, ?array $auth = null){
		parent::__construct($app_url);
		if(!is_null($auth)) $this->request->setHeader($auth);
		$this->api_url = "$this->app_url/emu/tradetask";
	}

}

?>