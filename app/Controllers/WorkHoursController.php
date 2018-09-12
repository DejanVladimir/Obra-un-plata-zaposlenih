<?php
namespace MyApp\Controllers;
Class WorkHoursController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Work hours');
		$this->data['employees'] = (new \MyApp\Models\EmployeeModel($this->data['database']))->get([]);
		$this->data['workhours'] = (new \MyApp\Models\WorkHoursModel($this->data['database']))->get([]);
		$employees = [];
		foreach($this->data['employees'] as $i => $item) {
			$employees[$item['employee_id']] = $item['last_name'] . ', ' . $item['first_name'] . ' (' . substr($item['born_at'], 0, 4) . ')';
		}
		foreach($this->data['workhours'] as $i => $item) {
			$item['employee_title'] = $employees[$item['employee_id']];
			$this->data['workhours'][$i] = $item;
		}
		return (new \MyApp\Views\WorkHoursView($this->data))->render();
	}
}