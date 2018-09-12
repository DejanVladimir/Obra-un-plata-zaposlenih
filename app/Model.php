<?php
namespace MyApp;
Class Model implements \MyApp\Interfaces\iModel {
	protected $database;
	public function __construct($database) {
		if(!$database instanceof \MyApp\Helpers\Database) {
			throw new \Exception('First argument must be an instance of \\MyApp\\Helpers\\Database!');
		}
		$this->database = $database;
	}
	protected function _constructWhere($filters) {
		return $this->database->constructWhere($filters);
	}
}