<?php
namespace MyApp\Models;
Class EmployeeModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `employee_id`, `first_name`, `last_name`, `born_at`, `workplace_title`, `is_archived`, `pay_grade_id` FROM `employee` $where";
		$stmt = $this->database->statement($query);
		if(count($filters) > 0) { $stmt->bind($param_types, ...$params); }
		$result = $stmt->execute()->fetch('employee_id', 'first_name', 'last_name', 'born_at', 'workplace_title', 'is_archived', 'pay_grade_id');
		$this->database->end();
		if($result === false) {
			throw new \Exception('There was an error fetching Employee data from database!');
		}
		return $result;
	}
	
	public function add($data = []) {
		$result = $this->database->preparedInsert('employee', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting Employee data into database!');
		}
		return $result;
	}
	
	public function update($id, $data = []) {
		$result = $this->database->preparedUpdate('employee', [ 'employee_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating Employee data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `employee` WHERE `employee_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting Employee data!');
		}
		return $result;
	}
}