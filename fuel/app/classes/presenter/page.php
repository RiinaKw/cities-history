<?php

/**
 * @package  App\Presenter
 */
class Presenter_Page extends Presenter_Layout
{
	public function view()
	{
		$this->title = $this->page->title;

		$html_content = Helper_Html::wiki($this->page->content);
		$this->description = Helper_Html::excerpt($html_content, 200);

		$this->og_type = 'article';
		$this->breadcrumbs = [
			'Top' => Helper_Uri::create('top'),
			$this->title => '',
		];
		$this->nav_item = $this->page->slug;
		$this->show_share = true;

		$this->set_safe('content', $html_content);
	}
	// function view()
}
// class Presenter_Page
