<?php
namespace MyApp\Models;
Class ReportsModel extends \MyApp\Model {
	public function __construct($database) {
		parent::__construct($database);
		$this->workhours = (new \MyApp\Models\WorkHoursModel($database));
		$this->employee = (new \MyApp\Models\EmployeeModel($database));
		$this->paygrades = (new \MyApp\Models\PayGradesModel($database));
		$this->benefits = (new \MyApp\Models\BenefitsModel($database));
	}
	public function listMonths($employee_id = false) {
		if(!$employee_id) {
			$wh = $this->workhours->get([]);
		} else {
			$wh = $this->workhours->get(['employee_id' => $employee_id]);
		}
		$months = [];
		foreach($wh as $item) {
			$months[] = substr($item['checked_at'], 0, 7);
		}
		$months = array_unique($months);
		return $months;
	}
	public function generateReport($employee_id = 'all', $date = '') {
		if(!$date) {
			$date = date('Y-m');
		}
		if(!is_numeric($employee_id) && $employee_id !== 'all') {
			$employee_id = 'all';
		}
		if($employee_id !== 'all') {
			try {
				$employees_get = $this->employee->get(['employee_id' => $employee_id]);
			} catch (\Exception $e) {
				$employees_get = [];
			}
			if(count($employees_get) < 1) {
				throw new \Exception('This employee doesn\'t exist!');
			}
			$wh = $this->workhours->get(['employee_id' => $employee_id]);
			$totalMinutes = 0;
			$days = [];
			foreach($wh as $item) {
				if(substr($item['checked_at'], 0, 7) != $date) { continue; }
				$start = $item['checked_at'] . ' ' . $item['checked_in_at'];
				$end = $item['checked_at'] . ' ' . $item['checked_out_at'];
				$start_time = strtotime($start);
				$end_time = strtotime($end);
				$minutes = round(abs($start_time - $end_time) / 60, 2);
				$totalMinutes += $minutes;
				$days[] = $item['checked_at'];
			}
			$days = array_unique($days);
			$actualTotalHoursFloat = $totalMinutes / 60;
			$totalHours = round($actualTotalHoursFloat);
			$leftoverMinutesTotal = round($totalMinutes - ($totalHours * 60));
			$employee = $employees_get[0];
			$employees_paygrade_id = $employee['pay_grade_id'];
			$paygrade_data = $this->paygrades->get(['pay_grade_id' => $employees_paygrade_id])[0];
			$paygrades_benefits_id = $paygrade_data['benefit_id'];
			$paygrades_max_hours = $paygrade_data['max_hours'];
			$paygrades_max_pay = $paygrade_data['max_pay'];
			$coeficientSalary = round(($paygrades_max_pay / $paygrades_max_hours), 2);
			$grossSalaryTotal = round($actualTotalHoursFloat * $coeficientSalary, 2);
			$benefit_data = $this->benefits->get(['benefit_id' => $paygrades_benefits_id])[0];
			$benefit_tax = $benefit_data['tax'] / 100;
			$benefit_disability_and_pension = $benefit_data['disability_and_pension'] / 100;
			$benefit_health_insurance = $benefit_data['health_insurance'] / 100;
			$benefit_unemployement = $benefit_data['unemployement'] / 100;
			$benefitsTaxTotal = $benefit_tax * $grossSalaryTotal;
			$benefitsDisabilityAndPensionTotal = $benefit_disability_and_pension * $grossSalaryTotal;
			$benefitsHealthInsuranceTotal = $benefit_health_insurance * $grossSalaryTotal;
			$benefitsUnemployementTotal = $benefit_unemployement * $grossSalaryTotal;
			$benefitsTotal = $benefitsUnemployementTotal + $benefitsHealthInsuranceTotal + $benefitsDisabilityAndPensionTotal + $benefitsTaxTotal;
			$netSalaryTotal = $grossSalaryTotal - $benefitsTotal;
			return [
				'employee_id' => $employee_id,
				'totalHours' => $totalHours,
				'leftoverMinutesTotal' => $leftoverMinutesTotal,
				'days' => $days,
				'grossSalaryTotal' => $grossSalaryTotal,
				'benefitsTaxTotal' => $benefitsTaxTotal,
				'benefitsDisabilityAndPensionTotal' => $benefitsDisabilityAndPensionTotal,
				'benefitsHealthInsuranceTotal' => $benefitsHealthInsuranceTotal,
				'benefitsUnemployementTotal' => $benefitsUnemployementTotal,
				'benefitsTotal' => $benefitsTotal,
				'netSalaryTotal' => $netSalaryTotal,
				'benefitsCategory' => $benefit_data,
				'payGrade' => $paygrade_data,
				'employee' => $employee,
				'is_all' => false
			];
		} else {
			$wh = $this->workhours->get([]);
			$employee_ids = [];
			foreach($wh as $item) {
				if(substr($item['checked_at'], 0, 7) != $date) { continue; }
				if(!in_array($item['employee_id'], $employee_ids)) {
					$employee_ids[] = $item['employee_id'];
				}
			}
			$full_report = [
				'employee_id' => null,
				'totalHours' => 0,
				'leftoverMinutesTotal' => 0,
				'days' => [],
				'grossSalaryTotal' => 0,
				'benefitsTaxTotal' => 0,
				'benefitsDisabilityAndPensionTotal' => 0,
				'benefitsHealthInsuranceTotal' => 0,
				'benefitsUnemployementTotal' => 0,
				'benefitsTotal' => 0,
				'netSalaryTotal' => 0,
				'benefitsCategory' => null,
				'payGrade' => null,
				'employee' => null,
				'is_all' => true
			];
			foreach($employee_ids as $employee_id) {
				$employee_report = $this->generateReport($employee_id, $date);
				$full_report['totalHours'] += $employee_report['totalHours'];
				$full_report['leftoverMinutesTotal'] += $employee_report['leftoverMinutesTotal'];
				$full_report['days'] = array_merge($full_report['days'], $employee_report['days']);
				$full_report['grossSalaryTotal'] += $employee_report['grossSalaryTotal'];
				$full_report['benefitsTaxTotal'] += $employee_report['benefitsTaxTotal'];
				$full_report['benefitsDisabilityAndPensionTotal'] += $employee_report['benefitsDisabilityAndPensionTotal'];
				$full_report['benefitsHealthInsuranceTotal'] += $employee_report['benefitsHealthInsuranceTotal'];
				$full_report['benefitsUnemployementTotal'] += $employee_report['benefitsUnemployementTotal'];
				$full_report['benefitsTotal'] += $employee_report['benefitsTotal'];
				$full_report['netSalaryTotal'] += $employee_report['netSalaryTotal'];
			}
			$full_report['days'] = array_unique($full_report['days']);
			return $full_report;
		}
	}
}