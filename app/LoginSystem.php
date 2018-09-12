<?php
namespace MyApp;
Class LoginSystem {
	public static final function isLoggedIn() {
		return isset($_SESSION[Configuration::SESSION_PREFIX . 'userlogin']) && $_SESSION[Configuration::SESSION_PREFIX . 'userlogin'];
	}
	
	public static final function getUsername() {
		if(LoginSystem::isLoggedIn()) {
			return $_SESSION[Configuration::SESSION_PREFIX . 'userlogin'];
		} else {
			return null;
		}
	}
	
	public static final function loginUser($username) {
		$_SESSION[Configuration::SESSION_PREFIX . 'userlogin'] = $username;
	}
	
	public static final function logoutUser() {
		$_SESSION[Configuration::SESSION_PREFIX . 'userlogin'] = null;
		unset($_SESSION[Configuration::SESSION_PREFIX . 'userlogin']);
	}
	
	public static final function restrictAccess() {
		if(!LoginSystem::isLoggedIn()) {
			Router::redirect('login');
		}
	}
	
	public static final function hashPassword($username, $password) {
		return hash('sha512', Configuration::PASSWORD_SALT . '$' . $username . '@' . $password);
	}
}