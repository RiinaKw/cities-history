<?php

use MyApp\Helper\Uri;
use MyApp\Helper\Breadcrumb;

/**
 * @package  App\Presenter
 */
class Presenter_Admin_Page_List extends Presenter_Layout
{
	public function view()
	{
		$this->pages = Model_Page::query()->where('deleted_at', null)->get();

		/*
		// 今はまだ使わない
		$this->url_add    = Uri::create('admin.page.add');
		$this->url_edit   = Uri::create('admin.page.edit');
		$this->url_delete = Uri::create('admin.page.delete');
		*/

		$this->title = '固定ページ一覧';
		$this->nav_item = 'admin-page';
		$this->breadcrumbs = Breadcrumb::forge('固定ページ');
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Admin_Reference_List
