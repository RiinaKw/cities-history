<?php

class Presenter_Page extends Presenter_Layout
{
	public function view()
	{
		$this->title = $this->page->title;
		$content = $this->page->content;
		$content = preg_replace('/<script\s.*?>.*?<\/script>/', '', $content);
		$this->description = preg_replace("/\s+/", ' ', strip_tags($content));
		$this->og_type = 'article';
		$this->breadcrumbs = [
			'Top' => Helper_Uri::create('top'),
			$this->title => '',
		];
		$this->nav_item = $this->page->slug;
		$this->show_share = true;

		$this->set_safe('content', $this->page->content);
	} // function view()
} // class Presenter_Page
