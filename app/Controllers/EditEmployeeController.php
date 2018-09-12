<?php
namespace MyApp\Controllers;
Class EditEmployeeController extends \MyApp\Controller {
	private $model;
	private $benefits;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->model = new \MyApp\Models\EmployeeModel($this->data['database']);
		$this->paygrades = new \MyApp\Models\PayGradesModel($this->data['database']);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
	}
	private function _processForm() {
		$paygrades = $this->paygrades;
		$model = $this->model;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$first_name = $requestData['first_name'];
		$last_name = $requestData['last_name'];
		$born_at = $requestData['born_at'];
		$workplace_title = $requestData['workplace_title'];
		$pay_grade_id = $requestData['pay_grade_id'];
		
		$this->data['fvalue_first_name'] = $first_name;
		$this->data['fvalue_last_name'] = $last_name;
		$this->data['fvalue_born_at'] = $born_at;
		$this->data['fvalue_workplace_title'] = $workplace_title;
		$this->data['fvalue_pay_grade_id'] = $pay_grade_id;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		// Filter inputs
		$first_name = trim($first_name);
		$last_name = trim($last_name);
		$born_at = trim($born_at);
		$workplace_title = trim($workplace_title);
		$pay_grade_id = (int) trim($pay_grade_id);
		
		/* VALIDATE FIRST NAME */
		if(strlen($first_name) < 1) {
			throw new \Exception('First name is too short!');
		}
		
		if(strlen($first_name) > 128) {
			throw new \Exception('First name is too long!');
		}
		
		if(!preg_match('/^[a-zA-Z0-9\._\-\sčćžđšČĆŽĐŠ]+$/', $first_name)) {
			throw new \Exception('First name is in an incorrect format!');
		}
		
		/* VALIDATE LAST NAME */
		if(strlen($last_name) < 1) {
			throw new \Exception('Last name is too short!');
		}
		
		if(strlen($last_name) > 128) {
			throw new \Exception('Last name is too long!');
		}
		
		if(!preg_match('/^[a-zA-Z0-9\._\-\sčćžđšČĆŽĐŠ]+$/', $last_name)) {
			throw new \Exception('Last name is in an incorrect format!');
		}
		
		/* VALIDATE DATE OF BIRTH */
		if(strlen($born_at) != 10) {
			throw new \Exception('Date of birth is in an incorrect format!');
		}
		
		if(!preg_match('/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/', $born_at)) {
			throw new \Exception('Date of birth is in an incorrect format!');
		}
		
		list($year_of_birth, $month_of_birth, $day_of_birth) = explode('-', $born_at);
		
		if($year_of_birth > (int) date('Y')) {
			throw new \Exception('Date of birth (Year) is invalid!');
		}
		
		if($year_of_birth < 1900) {
			throw new \Exception('Date of birth (Year) is invalid!');
		}
		
		if($month_of_birth > 12) {
			throw new \Exception('Date of birth (Month) is invalid!');
		}
		
		if($month_of_birth < 1) {
			throw new \Exception('Date of birth (Month) is invalid!');
		}
		
		if($day_of_birth > 31) {
			throw new \Exception('Date of birth (Day) is invalid!');
		}
		
		if($day_of_birth < 1) {
			throw new \Exception('Date of birth (Day) is invalid!');
		}
		
		/* VALIDATE WORKPLACE TITLE */
		if(strlen($workplace_title) < 1) {
			throw new \Exception('Workplace title is too short!');
		}
		
		if(strlen($workplace_title) > 128) {
			throw new \Exception('Workplace title is too long!');
		}
		
		if(!preg_match('/^[a-zA-Z0-9\._\-\sčćžđšČĆŽĐŠ]+$/', $workplace_title)) {
			throw new \Exception('Workplace title is in an incorrect format!');
		}
		
		/* VALIDATE PAY GRADE ID */
		try {
			$checkExistsPayGrade = $paygrades->get(['pay_grade_id' => $pay_grade_id]);
		} catch (\Exception $e) {
			$checkExistsPayGrade = [];
		}
		if(count($checkExistsPayGrade) > 0) {
			$existsPayGrade = true;
		} else {
			$existsPayGrade = false;
		}
		
		if($pay_grade_id <= 0 || !$existsPayGrade) {
			throw new \Exception('Pay grade is not valid!');
		}
		
		$result = $model->update($this->data['employee_id'], [
			'first_name' => $first_name,
			'last_name' => $last_name,
			'born_at' => $born_at,
			'workplace_title' => $workplace_title,
			'pay_grade_id' => $pay_grade_id
		]);
		
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
		$this->data['paygrades'] = $this->paygrades->get();
		$this->data['item'] = $items[0];
		$this->data['fvalue_first_name'] = $this->data['item']['first_name'];
		$this->data['fvalue_last_name'] = $this->data['item']['last_name'];
		$this->data['fvalue_born_at'] = $this->data['item']['born_at'];
		$this->data['fvalue_workplace_title'] = $this->data['item']['workplace_title'];
		$this->data['fvalue_pay_grade_id'] = $this->data['item']['pay_grade_id'];
		$this->data['title'] = $items[0]['last_name'] . ', ' . $items[0]['first_name'] . ' (' . $items[0]['born_at'] . ')';
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Edit Employee');
		return (new \MyApp\Views\EditEmployeeView($this->data))->render();
	}
}