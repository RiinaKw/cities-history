<?php

use MyApp\Abstracts\Controller;
use MyApp\Helper\Uri;

/**
 * The Page Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Page extends Controller
{
	public function action_index()
	{
		Uri::redirect('top');
	}
	// function action_index()

	public function action_detail()
	{
		$slug = $this->param('slug');

		$page = Model_Page::query()->where('slug', $slug)->get_one();
		if (! $page) {
			throw new HttpNotFoundException('ページが見つかりません。');
		}

		// create Presenter object
		$content = Presenter_Page::forge();
		$content->page = $page;

		return $content;
	}
	// function action_detail()
}
// class Controller_Page
