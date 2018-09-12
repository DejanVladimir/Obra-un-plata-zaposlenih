<?php
namespace MyApp\Controllers;
Class BenefitsController extends \MyApp\Controller {
	public function run(...$args) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Benefits');
		$this->data['benefits'] = (new \MyApp\Models\BenefitsModel($this->data['database']))->get([]);
		return (new \MyApp\Views\BenefitsView($this->data))->render();
	}
}