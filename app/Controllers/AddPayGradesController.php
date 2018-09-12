<?php 
namespace MyApp\Controllers;
Class AddPayGradesController extends \MyApp\Controller {
	private $benefits;
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
		$this->benefits = new \MyApp\Models\BenefitsModel($this->data['database']);
	}
	private function _processForm() {
		$benefits = $this->benefits;
		$model = new \MyApp\Models\PayGradesModel($this->data['database']);
		
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
		
		try {
			$checkExistingPayGrades = $model->get(['title' => $title]);
		} catch (\Exception $e) {
			$checkExistingPayGrades = [];
		}
		if(count($checkExistingPayGrades) > 0) {
			throw new \Exception('A Pay grade with that title already exists!');
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
		
		$result = $model->add([
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
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Add Pay grade');
		$this->data['benefits'] = $this->benefits->get();
		return (new \MyApp\Views\AddPayGradesView($this->data))->render();
	}
}