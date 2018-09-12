<?php 
namespace MyApp\Controllers;
Class EditUserController extends \MyApp\Controller {
	private $benefits;
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
		$this->model = (new \MyApp\Models\UserModel($this->data['database']));
		$this->employee = (new \MyApp\Models\EmployeeModel($this->data['database']));
	}
	private function _processForm() {
		$model = $this->model;
		$employee = $this->employee;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$username = $requestData['username'];
		$password = $requestData['password'];
		
		$this->data['fvalue_username'] = $username;
		
		// Filter inputs
		$username = trim($username);
		$password = trim($password);
		
		/* VALIDATE USERNAME */
		if(strlen($username) < 1) {
			throw new \Exception('Username is too short!');
		}
		
		if(strlen($username) > 128) {
			throw new \Exception('Username is too long!');
		}
		
		if(!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
			throw new \Exception('Username is in an incorrect format!');
		}
		
		/* VALIDATE PASSWORD */
		if(strlen($password) < 6) {
			throw new \Exception('Password is too short!');
		}
		
		if(strlen($password) > 128) {
			throw new \Exception('Password is too long!');
		}
		
		$password_hash = \MyApp\LoginSystem::hashPassword($username, $password);
		
		$result = $model->update($this->data['user_id'], [
			'username' => $username,
			'password' => $password_hash
		]);
		
		if($result) {
			\MyApp\Router::redirect('/users');
		}
	}
	public function run(...$args) {
		\MyApp\LoginSystem::restrictAccess();
		list($id) = $args;
		$this->data['user_id'] = $id;
		$items = $this->model->get([ 'user_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The User was not found. ');
		}
		$this->data['item'] = $items[0];
		$this->data['fvalue_username'] = $this->data['item']['username'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		$this->data['page_title'] = _i18n('Edit User');
		return (new \MyApp\Views\EditUserView($this->data))->render();
	}
}