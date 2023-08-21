<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GuardDriver;

class GuardValidate extends Command {

	protected $signature = 'guard:validate';

	protected $description = 'Weryfikacja spójności plików';

	protected $guard;

	public function __construct(){
		parent::__construct();
		$this->guard = new GuardDriver();
	}

	public function handle(){
		$errors = json_encode($this->guard->validate());
		$this->line($errors);
	}

}
