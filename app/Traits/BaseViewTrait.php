<?php 
namespace MyApp\Traits;
trait BaseViewTrait {
	public function wrap($data, $page_content) {
		$data['page_content'] = $page_content;
		return (new \MyApp\Views\BaseView($data))->render();
	}
}