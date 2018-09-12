<?php
namespace MyApp\Controllers;
Class EditBenefitsController extends \MyApp\Controller {
	private $model;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\BenefitsModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$model = $this->model;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$title = $requestData['title'];
		$tax = $requestData['tax'];
		$disability_and_pension = $requestData['disability_and_pension'];
		$health_insurance = $requestData['health_insurance'];
		$unemployement = $requestData['unemployement'];
		
		$this->data['fvalue_title'] = $title;
		$this->data['fvalue_tax'] = $tax;
		$this->data['fvalue_disability_and_pension'] = $disability_and_pension;
		$this->data['fvalue_health_insurance'] = $health_insurance;
		$this->data['fvalue_unemployement'] = $unemployement;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		// Filter inputs
		$title = trim($title);
		$tax = (float) trim($tax);
		$disability_and_pension = (float) trim($disability_and_pension);
		$health_insurance = (float) trim($health_insurance);
		$unemployement = (float) trim($unemployement);
		
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
		
		/* VALIDATE TAX */
		if($tax <= 0 || $tax > 100) {
			throw new \Exception('Tax is not a valid percentage!');
		}
		
		/* VALIDATE DISABILITY AND PENSION */
		if($disability_and_pension <= 0 || $disability_and_pension > 100) {
			throw new \Exception('Disability and pension benefits is not a valid percentage!');
		}
		
		/* VALIDATE HEALTH INSURANCE */
		if($health_insurance <= 0 || $health_insurance > 100) {
			throw new \Exception('Health insurance is not a valid percentage!');
		}
		
		/* VALIDATE UNEMPLOYEMENT */
		if($unemployement <= 0 || $unemployement > 100) {
			throw new \Exception('Unemployement benefits is not a valid percentage!');
		}
		
		$result = $model->update($this->data['benefit_id'], [
			'title' => $title,
			'tax' => $tax,
			'disability_and_pension' => $disability_and_pension,
			'health_insurance' => $health_insurance,
			'unemployement' => $unemployement
		]);
		
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
		$this->data['item'] = $items[0];
		$this->data['fvalue_title'] = $this->data['item']['title'];
		$this->data['fvalue_tax'] = $this->data['item']['tax'];
		$this->data['fvalue_disability_and_pension'] = $this->data['item']['disability_and_pension'];
		$this->data['fvalue_health_insurance'] = $this->data['item']['health_insurance'];
		$this->data['fvalue_unemployement'] = $this->data['item']['unemployement'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Edit Benefits category');
		return (new \MyApp\Views\EditBenefitsView($this->data))->render();
	}
}