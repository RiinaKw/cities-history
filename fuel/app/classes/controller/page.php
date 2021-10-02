<?php

/**
 * The Page Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Page extends Controller_Base
{
	public function action_index()
	{
		Helper_Uri::redirect('top');
	}
	// function action_index()

	public function action_detail()
	{
		$slug = $this->param('slug');

		$page = Model_Page::get_one_by_slug($slug);
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
