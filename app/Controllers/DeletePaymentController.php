<?php
namespace MyApp\Controllers;
Class DeletePaymentController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\PaymentModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		
		$requestData = \MyApp\Router::request()['data'];
		$csrf_token = $requestData['csrf_token'];
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		$result = $model->delete($this->data['payment_id']);
		
		if($result) {
			\MyApp\Router::redirect('/payments');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['payment_id'] = $id;
		$items = $this->model->get([ 'payment_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Payment was not found. ');
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
		$this->data['page_title'] = _i18n('Delete Payment');
		return (new \MyApp\Views\DeletePaymentView($this->data))->render();
	}
}