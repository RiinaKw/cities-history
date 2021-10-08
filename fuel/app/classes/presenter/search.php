<?php

/**
 * @package  App\Presenter
 */
class Presenter_Search extends Presenter_Layout
{
	public function view()
	{
		$title = "'{$this->q}'の検索結果";
		$description = "'{$this->q}'の検索結果";

		$this->title = $title;
		$this->description = $description;
		$this->robots = 'noindex,nofollow';
		$this->og_type = 'article';
		$this->breadcrumbs = \MyApp\Helper\Breadcrumb::forge('検索');
		$this->show_share = true;
	}
	// function view()
}
// class Presenter_Search
