<?php 
namespace MyApp\Views;
Class SkeletonView extends \MyApp\View {
	use \MyApp\Traits\BaseViewTrait;
	public function __construct($data = []) {
		parent::__construct();
		$this->view_name = 'skeleton';
		$this->data = $data;
		if(!isset($this->data['dont_display_sidebar']) || !$this->data['dont_display_sidebar']) {
			$this->data['sidebar'] = (new SidebarView($this->data))->render();
		}
		$this->data['display_sidebar'] = ((bool) (isset($this->data['sidebar']) && $this->data['sidebar']));
		$this->data['header'] = (new HeaderView($this->data))->render();
		$this->data['footer'] = (new FooterView($this->data))->render();
	}
	private function _render() {
		return parent::render();
	}
	public function render() {
		return $this->wrap($this->data, $this->_render());
	}
}