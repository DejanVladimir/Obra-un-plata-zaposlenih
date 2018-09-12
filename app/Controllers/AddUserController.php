<?php 
namespace MyApp\Controllers;
Class AddUserController extends \MyApp\Controller {
	private $paygrades;
	private $model;
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
		$this->model = (new \MyApp\Models\UserModel($this->data['database']));
	}
	private function _processForm() {
		$model = $this->model;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$username = $requestData['username'];
		$password = $requestData['password'];
		
		$this->data['fvalue_username'] = $username;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
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
		
		/* CHECK FOR DUPLICATE USERS WITH SAME USERNAME */
		try {
			$checkExistingUser = $model->get(['username' => $username]);
		} catch (\Exception $e) {
			$checkExistingUser = [];
		}
		if(count($checkExistingUser) > 0) {
			throw new \Exception('An user with the same Username already exists!');
		}
		
		$password_hash = \MyApp\LoginSystem::hashPassword($username, $password);
		
		$result = $model->add([
			'username' => $username,
			'password' => $password_hash
		]);
		
		if($result) {
			\MyApp\Router::redirect('/users');
		}
	}
	public function run(...$args) {
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Add User');
		return (new \MyApp\Views\AddUserView($this->data))->render();
	}
}