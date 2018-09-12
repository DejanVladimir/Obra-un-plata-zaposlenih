<?php
namespace MyApp\Views;
Class CheckinWorkHoursView extends \MyApp\View {
	use \MyApp\Traits\SkeletonViewTrait;
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'checkin_workhours';
		$this->data = $data;
	}
	private function _render() {
		return parent::render();
	}
	public function render() {
		return $this->wrap($this->data, $this->_render());
	}
}