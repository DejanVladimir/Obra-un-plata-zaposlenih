<?php 
namespace MyApp\Views;
Class BaseView extends \MyApp\View {
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'base';
		$this->data = $data;
	}
}