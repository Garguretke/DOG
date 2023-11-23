<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SchemaDriver;

class SchemaValidate extends Command {

	protected $signature = 'schema:validate';

	protected $description = 'Weryfikacja struktury bazy danych';

	protected $guard;

	public function __construct(){
		parent::__construct();
		$this->guard = new SchemaDriver(config('database.connections.mysql.database'), 'MySQL.ini');
	}

	public function handle(){
		$errors = json_encode($this->guard->validate());
		$this->line($errors);
	}

}
