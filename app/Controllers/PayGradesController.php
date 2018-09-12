<?php
namespace MyApp\Controllers;
Class PayGradesController extends \MyApp\Controller {
	public function run(...$args) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Pay grades');
		$this->data['paygrades'] = (new \MyApp\Models\PayGradesModel($this->data['database']))->get([]);
		return (new \MyApp\Views\PayGradesView($this->data))->render();
	}
}