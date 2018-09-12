<?php
namespace MyApp\Controllers;
Class DeleteEmployeeController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\EmployeeModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		
		$requestData = \MyApp\Router::request()['data'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		$result = $model->delete($this->data['employee_id']);
		
		if($result) {
			\MyApp\Router::redirect('/employees');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['employee_id'] = $id;
		$items = $this->model->get([ 'employee_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Employee was not found. ');
		}
		$this->data['title'] = $items[0]['last_name'] . ', ' . $items[0]['first_name'] . ' (' . $items[0]['born_at'] . ')';
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Delete Employee');
		return (new \MyApp\Views\DeleteEmployeeView($this->data))->render();
	}
}