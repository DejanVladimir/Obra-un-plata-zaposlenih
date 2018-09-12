<?php
namespace MyApp\Helpers;
class Database {
	protected $driver;
	
	public function __construct($db_host, $db_user, $db_pass, $db_name) {
		if(\MyApp\Configuration::DATABASE_DRIVER === 'mysqli') {
			$this->driver = new \MyApp\MySQLi_Database($db_host, $db_user, $db_pass, $db_name);
		} else {
			$this->driver = new \MyApp\PDO_Database($db_host, $db_user, $db_pass, $db_name);
		}
		return $this;
	}
	
	public function connect() {
		$this->driver->connect();
		return $this;
	}
	
	public function query($query) {
		return $this->driver->query($query);
	}
	
	public function statement($query) {
		$this->driver->statement($query);
		return $this;
	}
	
	public function preparedInsert($table, $data = [], $types_string = '') {
		if(!$this->_isValidKeyName($table)) {
			throw new \Exception('Table name is not valid!');
		}
		if(count($data) < 1) {
			throw new \Exception('Data cannot be empty!');
		}
		foreach($data as $key => $value) {
			if(!$this->_isValidKeyName($key)) {
				unset($data[$key]);
			}
		}
		if(count($data) < 1) {
			throw new \Exception('Data cannot be empty (after filtering invalid key names)!');
		}
		$fields = implode(', ', array_keys($data));
		$actual_values = array_values($data);
		$actual_keys = array_keys($data);
		$values_dummy = implode(', ', str_split(str_repeat('?', count($actual_values)), '1'));
		$query = "INSERT INTO `$table` ($fields) VALUES ($values_dummy)";
		if(!$types_string) {
			$types_string = '';
			foreach($actual_values as $i => $value) {
				if(is_numeric($value) || $actual_keys[$i] == 'id') {
					$types_string .= 'd';
				} else {
					$types_string .= 's';
				}
			}
		}
		$result = $this->statement($query)->bind($types_string, ...$actual_values)->execute();
		return $result;
	}
	
	private function _isValidKeyName($key) {
		if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
			return false;
		}
		return true;
	}
	
	public function constructWhere($filters) {
		$where = '';
		$params = []; $param_types = '';
		foreach($filters as $key => $value) {
			$lookup_type = 'AND';
			if(substr($key, -1) === '?') {
				$key = substr($key, 0, strlen($key) - 1);
				$lookup_type = 'OR';
			}
			if(!$this->_isValidKeyName($key)) {
				continue;
			}
			if($value === null) {
				$where .= ' ' . $lookup_type . '  `' . $key . '` IS NULL';
			} else {
				if(substr($value, 0, 1) === '%' && substr($value, -1) === '%') {
					$where .= ' ' . $lookup_type . '  `' . $key . '` LIKE ?';
				} else {
					$where .= ' ' . $lookup_type . ' `' . $key . '` = ?';
				}
				$params[] = $value; 
				if($key === 'id' || is_numeric($value)) { $param_types .= 'd'; } else { $param_types .= 's'; }
			}
		}
		$where = ltrim($where, ' AND ');
		$where = ltrim($where, ' OR ');
		if(trim($where) !== '') {
			$where = 'WHERE ' . $where;
		}
		return [$where, $params, $param_types];
	}
	
	public function preparedUpdate($table, $filters = [], $data = [], $types_string = '') {
		if(!$this->_isValidKeyName($table)) {
			throw new \Exception('Table name is not valid!');
		}
		if(count($data) < 1) {
			throw new \Exception('Data cannot be empty!');
		}
		foreach($data as $key => $value) {
			if(!$this->_isValidKeyName($key)) {
				unset($data[$key]);
			}
		}
		if(count($data) < 1) {
			throw new \Exception('Data cannot be empty (after filtering invalid key names)!');
		}
		list($where, $params, $param_types) = $this->constructWhere($filters);
		$sets = [];
		$actual_values = array_values($data);
		$actual_keys = array_keys($data);
		foreach($data as $key => $value) {
			$sets[] = '`' . $key . '` = ?';
		}
		$sets = implode(', ', $sets);
		$sets = rtrim($sets, ', ');
		$query = "UPDATE `$table` SET $sets $where";
		if(!$types_string) {
			$types_string = '';
			foreach($actual_values as $i => $value) {
				if(is_numeric($value) || $actual_keys[$i] == 'id') {
					$types_string .= 'd';
				} else {
					$types_string .= 's';
				}
			}
		}
		$types_string .= $param_types;
		$the_values = array_merge($actual_values, $params);
		$result = $this->statement($query)->bind($types_string, ...$the_values)->execute();
		return $result;
	}
	
	public function bind($types, ...$params) {
		if(strlen($types) < 1 || count($params) < 1) { return $this; }
		$this->driver->bind($types, ...$params);
		return $this;
	}
	
	public function execute() {
		$this->driver->execute();
		return $this;
	}
	
	public function fetch(...$params) {
		return $this->driver->fetch(...$params);
	}
	
	public function close() {
		$this->driver->close();
	}
	
	public function end() {
		$this->driver->end();
	}
}