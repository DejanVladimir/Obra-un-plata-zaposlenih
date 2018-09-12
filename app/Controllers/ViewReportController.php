<?php
namespace MyApp\Controllers;
Class ViewReportController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		list($employee_id, $date) = $params;
		$this->data['page_title'] = _i18n('Reports');
		$this->data['employees'] = (new \MyApp\Models\EmployeeModel($this->data['database']))->get([]);
		$this->data['workhours'] = (new \MyApp\Models\WorkHoursModel($this->data['database']))->get([]);
		$model = (new \MyApp\Models\ReportsModel($this->data['database']));
		$this->data['filter_employee_id'] = $employee_id;
		$this->data['filter_date'] = $date;
		$report = $model->generateReport($employee_id, $date);
		if($employee_id === 'all') {
			$title = _i18n('All employees');
		} else {
			$title = $report['employee']['last_name'] . ', ' . $report['employee']['first_name'] . ' (' . substr($report['employee']['born_at'], 0, 4) . ')';
		}
		$this->data['title'] = $title;
		$report['grossSalaryTotal'] = round($report['grossSalaryTotal'], 2);
		$report['benefitsTaxTotal'] = round($report['benefitsTaxTotal'], 2);
		$report['benefitsDisabilityAndPensionTotal'] = round($report['benefitsDisabilityAndPensionTotal'], 2);
		$report['benefitsHealthInsuranceTotal'] = round($report['benefitsHealthInsuranceTotal'], 2);
		$report['benefitsUnemployementTotal'] = round($report['benefitsUnemployementTotal'], 2);
		$report['benefitsTotal'] = round($report['benefitsTotal'], 2);
		$report['netSalaryTotal'] = round($report['netSalaryTotal'], 2);
		$days = $report['days'];
		uasort($days, function ($a, $b) {
			return strnatcmp($a, $b);
		});
		$days = array_values($days);
		$report['days_start'] = $days[0];
		$report['days_end'] = end($days);
		$this->data['report'] = $report;
		$this->data['dummy_output'] = print_r($report, true);
		$this->data['currency'] = \MyApp\Configuration::CURRENCY;
		return (new \MyApp\Views\ViewReportView($this->data))->render();
	}
}