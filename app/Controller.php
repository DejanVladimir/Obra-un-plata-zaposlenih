<?php
namespace MyApp;
Class Controller implements \MyApp\Interfaces\iController {
	protected $data;
	private $mustache;
	
	public function __construct($data = []) {
		$this->data = $data;
		return $this;
	}
	
	public function run(...$params) {
		throw new \Exception('Run was called but Controller wasn\'t setup properly!');
	}
}