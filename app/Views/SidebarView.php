<?php 
namespace MyApp\Views;
Class SidebarView extends \MyApp\View {
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'sidebar';
		$this->data = $data;
	}
}