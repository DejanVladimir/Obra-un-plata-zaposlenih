<?php 
namespace MyApp\Views;
Class HeaderView extends \MyApp\View {
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'header';
		$this->data = $data;
	}
}