<?php

use MyApp\Abstracts\Controller;

/**
 * The Page Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Page extends Controller
{
	public function action_index()
	{
		Helper_Uri::redirect('top');
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
		$content = Presenter::forge('page', 'view', null, 'page.tpl');
		$content->page = $page;

		return $content;
	}
	// function action_detail()
}
// class Controller_Page
