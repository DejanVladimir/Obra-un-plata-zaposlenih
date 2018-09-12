<?php 
namespace MyApp\Controllers;
Class EditWorkHoursController extends \MyApp\Controller {
	private $employee;
	
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
		$this->employee = new \MyApp\Models\EmployeeModel($this->data['database']);
		$this->model = new \MyApp\Models\WorkHoursModel($this->data['database']);
	}
	
	private function _processForm() {
		$employee = $this->employee;
		$model = $this->model;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$checked_in_at_0 = $requestData['checked_in_at_0'];
		$checked_in_at_1 = $requestData['checked_in_at_1'];
		$checked_in_at_2 = $requestData['checked_in_at_2'];
		$checked_out_at_0 = $requestData['checked_out_at_0'];
		$checked_out_at_1 = $requestData['checked_out_at_1'];
		$checked_out_at_2 = $requestData['checked_out_at_2'];
		$employee_id = $requestData['employee_id'];
		$checked_at = $requestData['checked_at'];
		
		$this->data['fvalue_checked_in_at_0'] = $checked_in_at_0;
		$this->data['fvalue_checked_in_at_1'] = $checked_in_at_1;
		$this->data['fvalue_checked_in_at_2'] = $checked_in_at_2;
		$this->data['fvalue_checked_out_at_0'] = $checked_out_at_0;
		$this->data['fvalue_checked_out_at_1'] = $checked_out_at_1;
		$this->data['fvalue_checked_out_at_2'] = $checked_out_at_2;
		$this->data['fvalue_employee_id'] = $employee_id;
		$this->data['fvalue_checked_at'] = $checked_at;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		// Filter inputs
		$checked_at = trim($checked_at);
		$checked_in_at_0 = (int) trim($checked_in_at_0);
		$checked_in_at_1 = (int) trim($checked_in_at_1);
		$checked_in_at_2 = (int) trim($checked_in_at_2);
		$checked_out_at_0 = (int) trim($checked_out_at_0);
		$checked_out_at_1 = (int) trim($checked_out_at_1);
		$checked_out_at_2 = (int) trim($checked_out_at_2);
		$employee_id = (int) trim($employee_id);
		
		// Construct full inputs
		$checked_in_at = '';
		$checked_in_at .= str_pad($checked_in_at_0, 2, '0', STR_PAD_LEFT);
		$checked_in_at .= ':';
		$checked_in_at .= str_pad($checked_in_at_1, 2, '0', STR_PAD_LEFT);
		$checked_in_at .= ':';
		$checked_in_at .= str_pad($checked_in_at_2, 2, '0', STR_PAD_LEFT);
		$checked_out_at = '';
		$checked_out_at .= str_pad($checked_out_at_0, 2, '0', STR_PAD_LEFT);
		$checked_out_at .= ':';
		$checked_out_at .= str_pad($checked_out_at_1, 2, '0', STR_PAD_LEFT);
		$checked_out_at .= ':';
		$checked_out_at .= str_pad($checked_out_at_2, 2, '0', STR_PAD_LEFT);
		
		/* VALIDATE DATE */
		if(strlen($checked_at) != 10) {
			throw new \Exception('Date is in an incorrect format!');
		}
		
		if(!preg_match('/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/', $checked_at)) {
			throw new \Exception('Date is in an incorrect format!');
		}
		
		list($year, $month, $day) = explode('-', $checked_at);
		
		if($year > (int) date('Y')) {
			throw new \Exception('Date (Year) is invalid!');
		}
		
		if($year < 1900) {
			throw new \Exception('Date (Year) is invalid!');
		}
		
		if($month > 12) {
			throw new \Exception('Date (Month) is invalid!');
		}
		
		if($month < 1) {
			throw new \Exception('Date (Month) is invalid!');
		}
		
		if($day > 31) {
			throw new \Exception('Date (Day) is invalid!');
		}
		
		if($day < 1) {
			throw new \Exception('Date (Day) is invalid!');
		}
		
		/* VALIDATE TIME */
		if($checked_in_at_0 > 23) {
			throw new \Exception('Time (Hours) is in an incorrect format!');
		}
		
		if($checked_in_at_1 > 59) {
			throw new \Exception('Time (Minutes) is in an incorrect format!');
		}
		
		if($checked_in_at_2 > 59) {
			throw new \Exception('Time (Seconds) is in an incorrect format!');
		}
		
		/* VALIDATE TIME */
		if($checked_out_at_0 > 23) {
			throw new \Exception('Time (Hours) is in an incorrect format!');
		}
		
		if($checked_out_at_1 > 59) {
			throw new \Exception('Time (Minutes) is in an incorrect format!');
		}
		
		if($checked_out_at_2 > 59) {
			throw new \Exception('Time (Seconds) is in an incorrect format!');
		}
		
		/* VALIDATE EMPLOYEE ID */
		try {
			$checkExistsEmployee = $employee->get(['employee_id' => $employee_id]);
		} catch (\Exception $e) {
			$checkExistsEmployee = [];
		}
		if(count($checkExistsEmployee) > 0) {
			$existsEmployee = true;
		} else {
			$existsEmployee = false;
		}
		
		if($employee_id <= 0 || !$existsEmployee) {
			throw new \Exception('Employee ID is not valid!');
		}
		
		$result = $model->update($this->data['work_hour_id'], [
			'checked_out_at' => $checked_out_at,
			'checked_in_at' => $checked_in_at,
			'checked_at' => $checked_at,
			'employee_id' => $employee_id
		]);
		
		if($result) {
			\MyApp\Router::redirect('/workhours');
		}
	}
	
	public function run(...$args) {
		\MyApp\LoginSystem::restrictAccess();
		list($id) = $args;
		$this->data['work_hour_id'] = $id;
		$items = $this->model->get([ 'work_hour_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Work hour was not found. ');
		}
		$this->data['item'] = $items[0];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		$this->data['page_title'] = _i18n('Edit workhours');
		$this->data['employees'] = $this->employee->get();
		$this->data['fvalue_checked_in_at'] = $this->data['item']['checked_in_at'];
		$this->data['fvalue_checked_in_at_0'] = substr($this->data['fvalue_checked_in_at'], 0, 2);
		$this->data['fvalue_checked_in_at_1'] = substr($this->data['fvalue_checked_in_at'], 3, 2);
		$this->data['fvalue_checked_in_at_2'] = substr($this->data['fvalue_checked_in_at'], 6, 2);
		$this->data['fvalue_checked_out_at'] = $this->data['item']['checked_out_at'];
		$this->data['fvalue_checked_out_at_0'] = substr($this->data['fvalue_checked_out_at'], 0, 2);
		$this->data['fvalue_checked_out_at_1'] = substr($this->data['fvalue_checked_out_at'], 3, 2);
		$this->data['fvalue_checked_out_at_2'] = substr($this->data['fvalue_checked_out_at'], 6, 2);
		$this->data['fvalue_checked_at'] = $this->data['item']['checked_at'];
		return (new \MyApp\Views\EditWorkHoursView($this->data))->render();
	}
}