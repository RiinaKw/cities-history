<?php

/**
 * @package  App\Presenter
 */
class Presenter_Admin_Page_List extends Presenter_Layout
{
	public function view()
	{
		$this->pages = Model_Page::query()->where('deleted_at', null)->get();

		$this->url_add    = Helper_Uri::create('admin.page.add');
		$this->url_edit   = Helper_Uri::create('admin.page.edit');
		$this->url_delete = Helper_Uri::create('admin.page.delete');

		$this->flash = Session::get_flash($this->flash_name);

		$this->title = '固定ページ一覧';
		$this->nav_item = 'admin-page';
		$this->breadcrumbs = ['固定ページ' => ''];
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Admin_Reference_List
