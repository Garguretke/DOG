<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GuardDriver;

class GuardGenerate extends Command {

	protected $signature = 'guard:generate';

	protected $description = 'Generating file checksums';

	protected $guard;

	public function __construct(){
		parent::__construct();
		$this->guard = new GuardDriver();
	}

	public function handle(){
		$this->line("<fg=green> $this->description</>");
		$this->guard->generate();
	}

}
