<?php
namespace MyApp; 
Class View implements \MyApp\Interfaces\iView {
	protected $data;
	protected $view_path;
	protected $view_name;
	
	public function __construct() {
		$this->view_path = __DIR__ . DIRECTORY_SEPARATOR . 'Templates';
		$this->mustache = new \Mustache_Engine();
	}
	
	private function _view() {
		if(!$this->view_name) {
			throw new \Exception('View name wasn\'t specified!');
		}
		if(!$this->view_path) {
			throw new \Exception('View path wasn\'t specified!');
		}
		return file_get_contents($this->view_path . DIRECTORY_SEPARATOR . $this->view_name . '.html');
	}
	
	private function _data() {
		return $this->data ?: [];
	}
	
	public function render() {
		if(!$this->view_name) {
			throw new \Exception('Render was called but View wasn\'t setup properly!');
		}
		$this->data['i18n'] = function($text, \Mustache_LambdaHelper $helper) {
			return _i18n($helper->render($text));
		};
		return $this->mustache->render($this->_view(), $this->_data());
	}
}