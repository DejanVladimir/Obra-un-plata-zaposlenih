<?php
namespace MyApp\Models;
Class UserModel extends \MyApp\Model {
	public function get($filters = []) {
		list($where, $params, $param_types) = parent::_constructWhere($filters);
		$query = "SELECT `user_id`, `username` FROM `user` $where";
		$result = $this->database->statement($query)->bind($param_types, ...$params)->execute()->fetch('user_id', 'username');
		$this->database->end();
		return $result;
	}
	
	public function add($data = []) {
		$result = $this->database->preparedInsert('user', $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error inserting User data into database!');
		}
		return $result;
	}
	
	public function update($id, $data = []) {
		$result = $this->database->preparedUpdate('user', [ 'user_id' => $id ], $data);
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error updating User data!');
		}
		return $result;
	}
	
	public function delete($id) {
		$query = "DELETE FROM `user` WHERE `user_id` = ?";
		$stmt = $this->database->statement($query);
		$stmt->bind('d', $id);
		$result = $stmt->execute();
		$this->database->end();
		if(!$result) {
			throw new \Exception('There was an error deleting User data!');
		}
		return $result;
	}
}