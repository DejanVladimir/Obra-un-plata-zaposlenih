<?php
namespace MyApp\Helpers;
Class CSRFProtection {
	public static final function validate($receivedToken) {
		$currentToken = \MyApp\Helpers\CSRFProtection::get();
		$result = ($currentToken === $receivedToken && $receivedToken && $currentToken);
		if(!$result) {
			throw new \Exception('Invalid or expired CSRF token!');
		}
	}
	
	public static final function generate() {
		$token = md5(openssl_random_pseudo_bytes(8));
		$_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf'] = $token;
		return $token;
	}
	
	public static final function expire() {
		$_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf'] = null;
		unset($_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf']);
	}
	
	public static final function get() {
		if(!isset($_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf'])) {
			$_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf'] = '';
		}
		$token = $_SESSION[\MyApp\Configuration::SESSION_PREFIX . 'csrf'];
		if(!$token) {
			return \MyApp\Helpers\CSRFProtection::generate();
		}
		return $token;
	}
}