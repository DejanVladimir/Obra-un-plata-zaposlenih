<?php
namespace MyApp\Controllers;
Class PaymentsController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Payments');
		$this->data['payments'] = (new \MyApp\Models\PaymentModel($this->data['database']))->get([]);
		return (new \MyApp\Views\PaymentsView($this->data))->render();
	}
}