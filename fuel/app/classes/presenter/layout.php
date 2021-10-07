<?php

/**
 * @package  App\Presenter
 */

use MyApp\Helper\Uri;

class Presenter_Layout extends Presenter_Base
{
	public function after()
	{
		// get view parameters
		$param = $this->_view->get();

		// create title
		$page_title = $param['title'];
		$site_title = Config::get('common.title') . '（' . Config::get('common.title_ja') . '）';
		if ($page_title) {
			$title = $page_title . ' - ' . $site_title;
		} else {
			$title = $site_title;
		}

		// set to template
		$this->q = Input::get('q');

		$this->user = Session::get('user');

		$this->title = $title;
		$this->page_title = $page_title;
		$this->og_type = isset($param['og_type']) ? $param['og_type'] : '';
		$this->nav_item = isset($param['nav_item']) ? $param['nav_item'] : '';
		$this->breadcrumbs = isset($param['breadcrumbs']) ? $param['breadcrumbs'] : [];
	}
	// function after()
}
// class Presenter_Layout
