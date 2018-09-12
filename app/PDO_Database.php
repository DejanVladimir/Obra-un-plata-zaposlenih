<?php 
namespace MyApp;
class PDO_Database implements \MyApp\Interfaces\iDatabase {
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;
	private $charset;
	
	protected $pdo;
	
	private $stmt;
	
	public function __construct($db_host, $db_user, $db_pass, $db_name) {
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_name = $db_name;
		$this->charset = 'utf8mb4';
		return $this;
	}
	
	public function connect() {
		$dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset={$this->charset}";
		$options = [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES => false
		];
		try {
			 $this->pdo = new \PDO($dsn, $this->db_user, $this->db_pass, $options);
		} catch (\PDOException $e) {
			 throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		return $this;
	}
	
	public function query($query) {
		return $this->pdo->query($query);
	}
	
	public function statement($query) {
		$this->stmt = $this->pdo->prepare($query);
		return $this;
	}
	
	public function bind($types, ...$params) {
		if(strlen($types) < 1 || count($params) < 1) { return $this; }
		if(isset($this->stmt) && $this->stmt) {
			foreach($params as $i => $value) {
				$type = \PDO::PARAM_STR;
				if(substr($types, $i, 1) === 'd') {
					$type = \PDO::PARAM_INT;
					$value = (int) $value;
				}
				$this->stmt->bindValue($i + 1, $value, $type);
			}
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
		if(isset($this->stmt) && $this->stmt) {
			if($this->stmt->rowCount() < 1) {
				return $data;
			}
			$_data = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach($_data as $i => $item) {
				foreach($item as $key => $value) {
					if(in_array($key, $fields)) {
						$data[$i][$key] = $value;
					}
				}
			}
		}
		return $data;
	}
	
	public function close() {
		return true;
	}
	
	public function end() {
		if(isset($this->stmt) && $this->stmt) {
			unset($this->stmt);
		}
		return true;
	}
}