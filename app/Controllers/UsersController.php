<?php
namespace MyApp\Controllers;
Class UsersController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Users');
		$this->data['users'] = (new \MyApp\Models\UserModel($this->data['database']))->get([]);
		return (new \MyApp\Views\UsersView($this->data))->render();
	}
}