<?php
namespace MyApp\Models;
Class WorkHoursModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `work_hour_id`, `checked_in_at`, `checked_out_at`, `checked_at`, `employee_id` FROM `work_hour` $where";
		$stmt = $this->database->statement($query);
		if(count($filters) > 0) { $stmt->bind($param_types, ...$params); }
		$result = $stmt->execute()->fetch('work_hour_id', 'checked_in_at', 'checked_out_at', 'checked_at', 'employee_id');
		$this->database->end();
		if($result === false) {
			throw new \Exception('There was an error fetching Work hours data from database!');
		}
		return $result;
	}
	
	public function add($data = []) {
		$result = $this->database->preparedInsert('work_hour', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting Work hours data into database!');
		}
		return $result;
	}
	
	public function update($id, $data = []) {
		$result = $this->database->preparedUpdate('work_hour', [ 'work_hour_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating Work hours data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `work_hour` WHERE `work_hour_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting Work hours data!');
		}
		return $result;
	}
}