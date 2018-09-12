<?php
namespace MyApp\Controllers;
Class ReportsController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Reports');
		$employee_model = new \MyApp\Models\EmployeeModel($this->data['database']);
		$workhours_model = new \MyApp\Models\WorkHoursModel($this->data['database']);
		$this->data['employees'] = $employee_model->get([]);
		$this->data['workhours'] = $workhours_model->get([]);
		$model = (new \MyApp\Models\ReportsModel($this->data['database']));
		$employee_id = false;
		$requestQS = \MyApp\Router::request()['query'];
		if(isset($requestQS['employee_id']) && $requestQS['employee_id']) {
			$employee_id = $requestQS['employee_id'];
		}
		$reports = $model->listMonths($employee_id);
		$months = [
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December'
		];
		$this->data['reports'] = [];
		foreach($reports as $i => $report) {
			$this->data['reports'][] = [
				'date' => $report,
				'year' => substr($report, 0, 4),
				'month' => substr($report, 5, 2),
				'month_name' => _i18n($months[(int) substr($report, 5, 2)]),
				'id' => $i + 1
			];
		}
		$this->data['filter_employee_id'] = $employee_id;
		$employee_get = $employee_model->get(['employee_id' => $employee_id]);
		if(count($employee_get) === 1) {
			$this->data['filter_employee'] = $employee_get[0];
			$this->data['filter_employee_title'] = $this->data['filter_employee']['last_name'] . ', ' . $this->data['filter_employee']['first_name'] . ' (' . $this->data['filter_employee']['born_at'] . ')';
		} else {
			$this->data['filter_employee'] = false;
			$this->data['filter_employee_title'] = '';
		}
		$this->data['for_employee'] = $employee_id ?: 'all';
		return (new \MyApp\Views\ReportsView($this->data))->render();
	}
}