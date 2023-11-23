<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SchemaDriver;

class SchemaGenerate extends Command {

	protected $signature = 'schema:generate';

	protected $description = 'Generowanie struktury bazy danych';

	protected $guard;

	public function __construct(){
		parent::__construct();
		$this->guard = new SchemaDriver(config('database.connections.mysql.database'), 'MySQL.ini');
	}

	public function handle(){
		$this->line("<fg=green> $this->description</>");
		$this->guard->generate();
	}

}
