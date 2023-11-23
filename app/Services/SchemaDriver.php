<?php

declare(strict_types=1);

namespace App\Services;

use DB;

class SchemaDriver {

	private string $file;
	private string $database;
	private array $schema;

	public function __construct(string $database, string $file = 'MySQL.ini'){
		$this->database = $database;
		$this->file = $file;
		$this->schema = [];
	}

	public function getTables() : array {
		$data = [];
		$tables = DB::select('SHOW TABLES');
		foreach($tables as $table) array_push($data, $table->{'Tables_in_'.$this->database});
		return $data;
	}

	public function getSchema() : array {
		if(!empty($this->schema)) return $this->schema;
		$tables = $this->getTables();
		$this->schema = [];
		foreach($tables as $table){
			$columns = DB::select("DESCRIBE `$table`");
			foreach($columns as $column){
				$key = $column->Field;
				unset($column->Field);
				$this->schema[$table][$key] = $column;
			}
		}
		return $this->schema;
	}

	public function reload() : void {
		$this->schema = [];
	}

	public function generate() : void {
		$schema = $this->getSchema();
		$guard = new IniFile($this->file, true);
		$guard->setAll($schema, true);
	}

	public function getOriginal() : array {
		$guard = new IniFile($this->file, true);
		return $guard->getAll();
	}

	public function validate() : array {
		$schema = $this->getSchema();
		$errors = [
			'missing_table' => [],
			'missing_column' => [],
			'unknown_table' => [],
			'unknown_column' => [],
			'wrong_value' => [],
			'repairable' => [],
		];
		$original = $this->getOriginal();
		foreach($original as $table_name => $table_data){
			if(!isset($schema[$table_name])){
				array_push($errors['missing_table'], $table_name);
				continue;
			}
			foreach($table_data as $column_name => $column_data){
				if(!isset($schema[$table_name][$column_name])){
					array_push($errors['missing_column'], [
						$table_name,
						$column_name
					]);
				} else {
					$restore = false;
					if($schema[$table_name][$column_name]->Type != $original[$table_name][$column_name]['Type']){
						array_push($errors['wrong_value'], [
							$table_name,
							$column_name,
							'Type',
							$schema[$table_name][$column_name]->Type,
							$original[$table_name][$column_name]['Type']
						]);
						$restore = true;
					}
					if($schema[$table_name][$column_name]->Null != $original[$table_name][$column_name]['Null']){
						array_push($errors['wrong_value'], [
							$table_name,
							$column_name,
							'Null',
							$schema[$table_name][$column_name]->Null,
							$original[$table_name][$column_name]['Null']
						]);
						$restore = true;
					}
					if($schema[$table_name][$column_name]->Key != $original[$table_name][$column_name]['Key']){
						array_push($errors['wrong_value'], [
							$table_name,
							$column_name,
							'Key',
							$schema[$table_name][$column_name]->Key,
							$original[$table_name][$column_name]['Key']
						]);
						$restore = true;
					}
					if($schema[$table_name][$column_name]->Default != $original[$table_name][$column_name]['Default']){
						array_push($errors['wrong_value'], [
							$table_name,
							$column_name,
							'Default',
							htmlspecialchars($schema[$table_name][$column_name]->Default ?? 'NULL'),
							htmlspecialchars($original[$table_name][$column_name]['Default'] ?? 'NULL')
						]);
						$restore = true;
					}
					if($schema[$table_name][$column_name]->Extra != $original[$table_name][$column_name]['Extra']){
						array_push($errors['wrong_value'], [
							$table_name,
							$column_name,
							'Extra',
							$schema[$table_name][$column_name]->Extra,
							$original[$table_name][$column_name]['Extra']
						]);
						$restore = true;
					}
					if($restore){
						if(!isset($errors['repairable'][$table_name])) $errors['repairable'][$table_name] = [];
						array_push($errors['repairable'][$table_name], $column_name);
					}
				}
			}
		}
		foreach($schema as $table_name => $table_data){
			if(!isset($original[$table_name])){
				array_push($errors['unknown_table'], $table_name);
				continue;
			}
			foreach($table_data as $column_name => $column_data){
				if(!isset($original[$table_name][$column_name])){
					array_push($errors['unknown_column'], [
						$table_name,
						$column_name
					]);
				}
			}
		}
		return $errors;
	}

	public function searchColumn(string $column) : array {
		$schema = $this->getSchema();
		$data = [];
		foreach($schema as $table_name => $table_data){
			if(isset($table_data[$column])){
				array_push($data, $table_name);
			}
		}
		return $data;
	}

	public function checkIndex(string $column) : array {
		$schema = $this->getSchema();
		$data = [];
		foreach($schema as $table_name => $table_data){
			if(isset($table_data[$column])){
				$data[$table_name] = !empty($schema[$table_name][$column]->Key);
			}
		}
		return $data;
	}

	public function haveIndex(string $table, string $column) : bool {
		$schema = $this->getSchema();
		if(!isset($schema[$table][$column])) return false;
		return !empty($schema[$table][$column]->Key);
	}

	public function get_sql_from_original(array $original, string $table, string $column) : string {
		$type = $original[$table][$column]['Type'];
		$extra = $original[$table][$column]['Extra'];
		$nullable = (($original[$table][$column]['Null'] == 'YES') ? 'NULL' : 'NOT NULL');
		if($nullable == 'NULL' && is_null($original[$table][$column]['Default'])){
			$default = 'DEFAULT NULL';
		} else if(isset($original[$table][$column]['Default'])){
			$default = 'DEFAULT '.$original[$table][$column]['Default'];
		} else {
			$default = '';
		}
		return "`$column` $type $nullable $default $extra";
	}

	public function get_key_from_original(array $original, string $table, string $column){
		if($original[$table][$column]['Key'] == 'PRI'){
			return "PRIMARY KEY(`$column`)";
		} else if($original[$table][$column]['Key'] == 'MUL'){
			return "INDEX `".$table."_".$column."_index` (`$column`)";
		} else if($original[$table][$column]['Key'] == 'UNI'){
			return "UNIQUE `".$table."_".$column."_unique` (`$column`)";
		}
		return "";
	}

	public function repair_column(array $original, string $table, string $column) : void {
		$sql = $this->get_sql_from_original($original, $table, $column);
		DB::statement("ALTER TABLE `$table` MODIFY $sql");
		$sql = $this->get_key_from_original($original, $table, $column);
		if($sql != '') DB::statement("ALTER TABLE `$table` ADD $sql");
	}

	public function restore_column(array $original, string $table, string $column) : void {
		$sql = $this->get_sql_from_original($original, $table, $column);
		DB::statement("ALTER TABLE `$table` ADD $sql");
		$sql = $this->get_key_from_original($original, $table, $column);
		if($sql != '') DB::statement("ALTER TABLE `$table` ADD $sql");
	}

	public function restore_table(array $original, string $table) : void {
		$column_data = [];
		$keys = [];
		$columns = array_keys($original[$table]);
		foreach($columns as $column){
			array_push($column_data, $this->get_sql_from_original($original, $table, $column));
			if($original[$table][$column]['Key'] == 'PRI'){
				array_push($keys, "PRIMARY KEY (`$column`)");
			} else if($original[$table][$column]['Key'] == 'MUL'){
				array_push($keys, "KEY `".$table."_".$column."_index` (`$column`)");
			} else if($original[$table][$column]['Key'] == 'UNI'){
				array_push($keys, "UNIQUE KEY `".$table."_".$column."_unique` (`$column`)");
			}
		}
		$sql = implode(", ", array_merge($column_data, $keys));
		DB::statement("CREATE TABLE `$table` ($sql)");
	}

}

?>
