<?php
namespace MyApp\Controllers;
Class EditPayGradesController extends \MyApp\Controller {
	private $model;
	private $benefits;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\PayGradesModel($this->data['database']);
		$this->benefits = new \MyApp\Models\BenefitsModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$benefits = $this->benefits;
		$model = $this->model;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$title = $requestData['title'];
		$max_hours = $requestData['max_hours'];
		$max_pay = $requestData['max_pay'];
		$benefit_id = $requestData['benefit_id'];
		$this->data['fvalue_title'] = $title;
		$this->data['fvalue_max_hours'] = $max_hours;
		$this->data['fvalue_max_pay'] = $max_pay;
		$this->data['fvalue_benefit_id'] = $benefit_id;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		// Filter inputs
		$title = trim($title);
		$max_hours = (int) trim($max_hours);
		$max_pay = (float) trim($max_pay);
		$benefit_id = (int) trim($benefit_id);
		
		/* VALIDATE TITLE */
		if(strlen($title) < 1) {
			throw new \Exception('Title is too short!');
		}
		
		if(strlen($title) > 128) {
			throw new \Exception('Title is too long!');
		}
		
		if(!preg_match('/^[a-zA-Z0-9\._\-\sčćžđšČĆŽĐŠ]+$/', $title)) {
			throw new \Exception('Title is in an incorrect format!');
		}
		
		/* VALIDATE MAX HOURS */
		if($max_hours <= 0 || $max_hours > 504) {
			throw new \Exception('Maximum hours is not valid!');
		}
		
		/* VALIDATE MAX PAY */
		if($max_pay <= 0) {
			throw new \Exception('Maximum pay is not valid!');
		}
		
		/* VALIDATE BENEFITS CATEGORY */
		try {
			$checkExistsBenefits = $benefits->get(['benefit_id' => $benefit_id]);
		} catch (\Exception $e) {
			$checkExistsBenefits = [];
		}
		if(count($checkExistsBenefits) > 0) {
			$existsBenefits = true;
		} else {
			$existsBenefits = false;
		}
		
		if($benefit_id <= 0 || !$existsBenefits) {
			throw new \Exception('Benefits category is not valid!');
		}
		
		$result = $model->update($this->data['pay_grade_id'], [
			'title' => $title,
			'max_hours' => $max_hours,
			'max_pay' => $max_pay,
			'benefit_id' => $benefit_id
		]);
		
		if($result) {
			\MyApp\Router::redirect('/paygrades');
		}
	}
	public function run(...$args) {
		list($id) = $args;
		$this->data['pay_grade_id'] = $id;
		$items = $this->model->get([ 'pay_grade_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Pay grade was not found. ');
		}
		$this->data['benefits'] = $this->benefits->get();
		$this->data['item'] = $items[0];
		$this->data['fvalue_title'] = $this->data['item']['title'];
		$this->data['fvalue_max_hours'] = $this->data['item']['max_hours'];
		$this->data['fvalue_max_pay'] = $this->data['item']['max_pay'];
		$this->data['fvalue_benefit_id'] = $this->data['item']['benefit_id'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Edit Pay grade');
		return (new \MyApp\Views\EditPayGradesView($this->data))->render();
	}
}