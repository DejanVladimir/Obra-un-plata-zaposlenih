<?php 
namespace MyApp\Traits;
trait SkeletonViewTrait {
	public function wrap($data, $page_content) {
		$data['main'] = $page_content;
		return (new \MyApp\Views\SkeletonView($data))->render();
	}
}