<?php
namespace MyApp\Controllers;
Class EmployeesController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Employees');
		$this->data['employees'] = (new \MyApp\Models\EmployeeModel($this->data['database']))->get([]);
		return (new \MyApp\Views\EmployeesView($this->data))->render();
	}
}