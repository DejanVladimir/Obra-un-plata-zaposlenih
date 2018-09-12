<?php
namespace MyApp\Models;
Class PaymentModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `payment_id`, `employee_id`, `month_worked_at`, `amount_paid`, `paid_at`, `is_paid_fully`, `payment_detail` FROM `payment` $where";
		$results = $this->database->statement($query)->bind($param_types, ...$params)->execute()->fetch('payment_id', 'employee_id', 'month_worked_at', 'amount_paid', 'paid_at', 'is_paid_fully', 'payment_detail');
		$this->database->end();
		/*foreach($results as $i => $result) {
			$results[$i]['month_worked_at'] = substr($result['month_worked_at'], 0, 7);
		}*/
		return $results;
	}
	
	public function add($data = []) {
		$result = $this->database->preparedInsert('payment', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting Payment data into database!');
		}
		return $result;
	}
	
	public function update($id, $data = []) {
		$result = $this->database->preparedUpdate('payment', [ 'payment_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating Payment data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `payment` WHERE `payment_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting Payment data!');
		}
		return $result;
	}
}