<?php
namespace MyApp\Views;
Class DeletePayGradesView extends \MyApp\View {
	use \MyApp\Traits\SkeletonViewTrait;
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'delete_paygrades';
		$this->data = $data;
	}
	private function _render() {
		return parent::render();
	}
	public function render() {
		return $this->wrap($this->data, $this->_render());
	}
}