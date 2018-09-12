<?php 
namespace MyApp;
Class Router implements \MyApp\Interfaces\iRouter {
	protected $data;
	public function __construct($data = []) {
		$this->data = $data;
	}
	public function bind($path, $controllerName) {
		$controllerName = '\\MyApp\\Controllers\\' . $controllerName;
		$data = $this->data;
		\Flight::route($path, function(...$args) use ($data, $controllerName) {
			echo (new $controllerName($data))->run(...$args);
		});
	}
	public function run() {
		\Flight::start();
	}
	public static final function redirect($url = '/', $www_prefix = true) {
		if($www_prefix) {
			$url = ltrim($url, '/');
			$url = Configuration::WWW . $url;
		}
		\Flight::redirect($url);
		\Flight::stop();
	}
	public static final function request() {
		$frequest = \Flight::request();
		$request = [
			'method' => $frequest->method,
			'data' => $frequest->data,
			'query' => $frequest->query,
			'ip' => $frequest->ip,
			'cookies' => $frequest->cookies,
			'files' => $frequest->files
		];
		return $request;
	}
}