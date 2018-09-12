<?php
namespace MyApp\Controllers;
Class LoginController extends \MyApp\Controller {
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processLogin() {
		$requestData = \MyApp\Router::request()['data'];
		$username = $requestData['username'];
		$password = $requestData['password'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		$userModel = new \MyApp\Models\UserModel($this->data['database']);
		$password_hash = \MyApp\LoginSystem::hashPassword($username, $password);
		$users = $userModel->get([ 'username' => $username, 'password' => $password_hash ]);
		if($users && isset($users[0]) && isset($users[0]['user_id']) && (int) $users[0]['user_id'] > 0) {
			\MyApp\LoginSystem::loginUser($users[0]['username']);
			\MyApp\Router::redirect('/');
		} else {
			$this->data['error'] = _i18n('Wrong username and/or password. ');
		}
	}
	public function run(...$args) {
		if(\MyApp\LoginSystem::isLoggedIn()) {
			Router::redirect('/');
		}
		if(\MyApp\Router::request()['method'] === 'POST') {
			$this->_processLogin();
		}
		$this->data['page_title'] = _i18n('Login');
		return (new \MyApp\Views\LoginView($this->data))->render();
	}
}