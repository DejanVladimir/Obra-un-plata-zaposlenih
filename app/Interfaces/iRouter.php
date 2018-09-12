<?php 
namespace MyApp\Interfaces;
interface iRouter {
	public function __construct();
	public function bind($path, $controllerName);
	public function run();
	public static function redirect($url = '/', $www_prefix = true);
	public static function request();
}