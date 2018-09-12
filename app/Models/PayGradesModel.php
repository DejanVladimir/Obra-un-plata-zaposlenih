<?php
namespace MyApp\Models;
Class PayGradesModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `pay_grade_id`, `title`, `max_hours`, `max_pay`, `benefit_id` FROM `pay_grade` $where";
		$stmt = $this->database->statement($query);
		if(count($filters) > 0) { $stmt->bind($param_types, ...$params); }
		$stmt->execute();
		$result = $stmt->fetch('pay_grade_id', 'title', 'max_hours', 'max_pay', 'benefit_id');
		$this->database->end();
		if($result === false) {
			throw new \Exception('There was an error fetching Pay grades data from database!');
		}
		return $result;
	}
	
	public function add($data) {
		$result = $this->database->preparedInsert('pay_grade', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting Pay grades data into database!');
		}
		return $result;
	}
	
	public function update($id, $data) {
		$result = $this->database->preparedUpdate('pay_grade', [ 'pay_grade_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating Pay grades data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `pay_grade` WHERE `pay_grade_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting Pay grades data!');
		}
		return $result;
	}
}