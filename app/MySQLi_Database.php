<?php
namespace MyApp;
class MySQLi_Database implements \MyApp\Interfaces\iDatabase {
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;
	
	protected $mysqli;
	
	private $stmt;
	
	public function __construct($db_host, $db_user, $db_pass, $db_name) {
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_name = $db_name;
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		return $this;
	}
	
	public function connect() {
		$this->mysqli = new \mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
		$this->mysqli->query("SET names 'utf8mb4'");
		return $this;
	}
	
	public function query($query) {
		return $this->mysqli->query($query);
	}
	
	public function statement($query) {
		$this->stmt = $this->mysqli->prepare($query);
		return $this;
	}
	
	public function bind($types, ...$params) {
		if(strlen($types) < 1 || count($params) < 1) { return $this; }
		if(isset($this->stmt) && $this->stmt) {
			$this->stmt->bind_param($types, ...$params);
			return $this;
		} else {
			throw new \Exception('No Prepared Statement to bind parameters was initialized!');
		}
	}
	
	public function execute() {
		if(isset($this->stmt) && $this->stmt) {
			$this->stmt->execute();
			return $this;
		} else {
			throw new \Exception('No Prepared Statement to execute was initialized!');
		}
	}
	
	public function fetch(...$fields) {
		$data = [];
		$fields_keys = array_values($fields);
		if(isset($this->stmt) && $this->stmt) {
			$this->stmt->store_result();
			if($this->stmt->num_rows < 1) {
				return $data;
			}
			$this->stmt->bind_result(...$fields);
			while ($this->stmt->fetch()) {
				$fetched_values = [];
				foreach($fields as $i => $value) {
					$fetched_values[$fields_keys[$i]] = $value;
				}
				$data[] = $fetched_values;
			}
		}
		return $data;
	}
	
	public function close() {
		$this->mysqli->close();
	}
	
	public function end() {
		if(isset($this->stmt) && $this->stmt) {
			$this->stmt->close();
			unset($this->stmt);
		}
	}
}