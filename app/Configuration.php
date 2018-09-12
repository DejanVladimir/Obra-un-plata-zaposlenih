<?php 
namespace MyApp;
final class Configuration {
	const WWW = 'http://localhost/fv_racun_v3/';
	const APP_NAME = 'MyApp';
	const SESSION_PREFIX = 'myapp_';
	const DATABASE_HOST = 'localhost';
	const DATABASE_USER = 'root';
	const DATABASE_PASS = '';
	const DATABASE_NAME = 'fv_racun_v3';
	const APP_LANGUAGE = 'sr_RS';
	const I18N_LOG_UNKNOWN_STRINGS = false;
	const DEFAULT_LANGUAGE = 'en_US';
	const CURRENCY = 'RSD';
	const PASSWORD_SALT = '1CD1699E25D459D9BDAA2E2841B43';
	// const DATABASE_DRIVER = 'mysqli';
	const DATABASE_DRIVER = 'pdo';
}