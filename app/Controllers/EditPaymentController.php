<?php 
namespace MyApp\Controllers;
Class EditPaymentController extends \MyApp\Controller {
	private $benefits;
	public function __construct($data = []) {
		parent::__construct($data);
		$this->data['csrf_token'] = \MyApp\Helpers\CSRFProtection::get();
		$this->model = (new \MyApp\Models\PaymentModel($this->data['database']));
		$this->employee = (new \MyApp\Models\EmployeeModel($this->data['database']));
	}
	private function _processForm() {
		$model = $this->model;
		$employee = $this->employee;
		
		// Define inputs from RequestData
		$requestData = \MyApp\Router::request()['data'];
		
		$csrf_token = $requestData['csrf_token'];
		$month_worked_at_0 = $requestData['month_worked_at_0'];
		$month_worked_at_1 = $requestData['month_worked_at_1'];
		$employee_id = $requestData['employee_id'];
		$amount_paid = $requestData['amount_paid'];
		$paid_at = $requestData['paid_at'];
		$payment_detail = $requestData['payment_detail'];
		$is_paid_fully = $requestData['is_paid_fully'] == '1' ? true : false;
		
		$this->data['fvalue_month_worked_at_0'] = $month_worked_at_0;
		$this->data['fvalue_month_worked_at_1'] = $month_worked_at_1;
		$this->data['fvalue_is_paid_fully'] = $is_paid_fully;
		$this->data['fvalue_employee_id'] = $employee_id;
		$this->data['fvalue_amount_paid'] = $amount_paid;
		$this->data['fvalue_paid_at'] = $paid_at;
		$this->data['fvalue_payment_detail'] = $payment_detail;
		
		\MyApp\Helpers\CSRFProtection::validate($csrf_token);
		\MyApp\Helpers\CSRFProtection::expire();
		
		// Filter inputs
		$amount_paid = (float) str_replace(',', '.', trim($amount_paid));
		$paid_at = trim($paid_at);
		$payment_detail = trim($payment_detail);
		$employee_id = (int) trim($employee_id);
		$month_worked_at_1 = (int) trim($month_worked_at_1);
		$month_worked_at_0 = (int) trim($month_worked_at_0);
		
		/* VALIDATE PAYMENT DETAIL */
		if(strlen($payment_detail) > 512) {
			throw new \Exception('Payment detail is too long!');
		}
		
		/* VALIDATE DATE PAID */
		if(strlen($paid_at) != 10) {
			throw new \Exception('Date paid is in an incorrect format!');
		}
		
		if(!preg_match('/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/', $paid_at)) {
			throw new \Exception('Date paid is in an incorrect format!');
		}
		
		list($year, $month, $day) = explode('-', $paid_at);
		
		if($year > (int) date('Y')) {
			throw new \Exception('Date paid (Year) is invalid!');
		}
		
		if($year < 1900) {
			throw new \Exception('Date paid (Year) is invalid!');
		}
		
		if($month > 12) {
			throw new \Exception('Date paid (Month) is invalid!');
		}
		
		if($month < 1) {
			throw new \Exception('Date paid (Month) is invalid!');
		}
		
		if($month > 31) {
			throw new \Exception('Date paid (Day) is invalid!');
		}
		
		if($month < 1) {
			throw new \Exception('Date paid (Day) is invalid!');
		}
		
		/* VALIDATE MONTH WORKED AT */
		if($month_worked_at_0 > (int) date('Y') || $month_worked_at_0 < 1900) {
			throw new \Exception('Month worked at (Year) is in an incorrect format!');
		}
		
		if($month_worked_at_1 < 1 || $month_worked_at_1 > 12) {
			throw new \Exception('Month worked at (Month) is in an incorrect format!');
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
		
		/* VALIDATE MAX PAY */
		if($amount_paid <= 0) {
			throw new \Exception('Amount paid is not valid!');
		}
		
		$month_worked_at_1 = str_pad($month_worked_at_1, 2, '0', STR_PAD_LEFT);
		
		$month_worked_at = $month_worked_at_0 . '-' . $month_worked_at_1; // . '-01';
		
		$result = $model->update($this->data['payment_id'], [
			'employee_id' => $employee_id,
			'amount_paid' => $amount_paid,
			'paid_at' => $paid_at,
			'payment_detail' => $payment_detail,
			'month_worked_at' => $month_worked_at,
			'is_paid_fully' => $is_paid_fully ? '1' : '0'
		]);
		
		if($result) {
			\MyApp\Router::redirect('/payments');
		}
	}
	public function run(...$args) {
		\MyApp\LoginSystem::restrictAccess();
		list($id) = $args;
		$this->data['payment_id'] = $id;
		$items = $this->model->get([ 'payment_id' => $id ]);
		if(count($items) < 1) {
			throw new \Exception('The Payment was not found. ');
		}
		$this->data['item'] = $items[0];
		$this->data['fvalue_employee_id'] = $this->data['item']['employee_id'];
		$this->data['fvalue_amount_paid'] = $this->data['item']['amount_paid'];
		$this->data['fvalue_paid_at'] = $this->data['item']['paid_at'];
		$this->data['fvalue_payment_detail'] = $this->data['item']['payment_detail'];
		$this->data['fvalue_month_worked_at'] = $this->data['item']['month_worked_at'];
		$this->data['fvalue_month_worked_at_0'] = substr($this->data['item']['month_worked_at'], 0, 4);
		$this->data['fvalue_month_worked_at_1'] = substr($this->data['item']['month_worked_at'], 5, 7);
		$this->data['fvalue_is_paid_fully'] = $this->data['item']['is_paid_fully'];
		if(\MyApp\Router::request()['method'] === 'POST') {
			try {
				$this->_processForm();
			} catch (\Exception $e) {
				$this->data['error'] = $e->getMessage();
			}
		}
		$this->data['page_title'] = _i18n('Edit Payment');
		$this->data['employees'] = $this->employee->get([]);
		return (new \MyApp\Views\EditPaymentView($this->data))->render();
	}
}