<?php

use MyApp\Helper\Breadcrumb;
use MyApp\Helper\Str;

/**
 * @package  Fuel\Presenter
 */
class Presenter_Page extends Presenter_Layout
{
	public function view()
	{
		$this->title = $this->page->title;

		$html_content = Str::wiki($this->page->content);
		$this->description = Str::excerpt($html_content, 200);

		$this->og_type = 'article';
		$this->breadcrumbs = Breadcrumb::forge($this->title);

		$this->nav_item = $this->page->slug;
		$this->show_share = true;

		$this->set_safe('content', $html_content);
	}
	// function view()
}
// class Presenter_Page
