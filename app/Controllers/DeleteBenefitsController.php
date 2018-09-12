<?php
namespace MyApp\Controllers;
Class DeleteBenefitsController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\BenefitsModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		
		$requestData = \MyApp\Router::request()['data'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		$result = $model->delete($this->data['benefit_id']);
		
		if($result) {
			\MyApp\Router::redirect('/benefits');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['benefit_id'] = $id;
		$items = $this->model->get([ 'benefit_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Benefits category was not found. ');
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
		$this->data['page_title'] = _i18n('Delete Benefits category');
		return (new \MyApp\Views\DeleteBenefitsView($this->data))->render();
	}
}