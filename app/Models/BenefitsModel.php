<?php
namespace MyApp\Models;
Class BenefitsModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `benefit_id`, `title`, `tax`, `disability_and_pension`, `health_insurance`, `unemployement` FROM `benefit` $where";
		$stmt = $this->database->statement($query);
		if(count($filters) > 0) { $stmt->bind($param_types, ...$params); }
		$stmt->execute();
		$result = $stmt->fetch('benefit_id', 'title', 'tax', 'disability_and_pension', 'health_insurance', 'unemployement');
		$this->database->end();
		if($result === false) {
			throw new \Exception('There was an error fetching Benefits data from database!');
		}
		return $result;
	}
	
	public function add($data) {
		$result = $this->database->preparedInsert('benefit', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting Benefits data into database!');
		}
		return $result;
	}
	
	public function update($id, $data) {
		$result = $this->database->preparedUpdate('benefit', [ 'benefit_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating Benefits data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `benefit` WHERE `benefit_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting Benefits data!');
		}
		return $result;
	}
}