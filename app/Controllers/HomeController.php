<?php 
namespace MyApp\Controllers;
Class HomeController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		$this->data['page_title'] = _i18n('Home');
		return (new \MyApp\Views\HomeView($this->data))->render();
	}
}