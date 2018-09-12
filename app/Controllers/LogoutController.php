<?php
namespace MyApp\Controllers;
Class LogoutController extends \MyApp\Controller {
	public function run(...$params) {
		\MyApp\LoginSystem::restrictAccess();
		\MyApp\LoginSystem::logoutUser();
		\MyApp\Router::redirect('/');
	}
}