<?php 
namespace MyApp\Views;
Class FooterView extends \MyApp\View {
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'footer';
		$this->data = $data;
	}
}