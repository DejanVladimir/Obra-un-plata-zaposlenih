<?php
namespace MyApp\Controllers;
Class DeleteWorkHoursController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\WorkHoursModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		
		$requestData = \MyApp\Router::request()['data'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		$result = $model->delete($this->data['work_hour_id']);
		
		if($result) {
			\MyApp\Router::redirect('/workhours');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['work_hour_id'] = $id;
		$items = $this->model->get([ 'work_hour_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Work hours was not found. ');
		}
		$this->data['title'] = $items[0]['title'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Delete Work hours');
		return (new \MyApp\Views\DeleteWorkHoursView($this->data))->render();
	}
}