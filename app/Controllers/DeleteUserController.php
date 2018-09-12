<?php
namespace MyApp\Controllers;
Class DeleteUserController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\UserModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		
		$requestData = \MyApp\Router::request()['data'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		$result = $model->delete($this->data['user_id']);
		
		if($result) {
			\MyApp\Router::redirect('/users');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['user_id'] = $id;
		$items = $this->model->get([ 'user_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The User was not found. ');
		}
		$this->data['title'] = $items[0]['username'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Delete User');
		return (new \MyApp\Views\DeleteUserView($this->data))->render();
	}
}