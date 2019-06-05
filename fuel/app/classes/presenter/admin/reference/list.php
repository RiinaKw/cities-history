<?php

class Presenter_Admin_Reference_List extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		$this->dates = Model_Referencedate::get_all();

		$this->url_add    = Helper_Uri::create('admin.reference.add');
		$this->url_edit   = Helper_Uri::create('admin.reference.edit');
		$this->url_delete = Helper_Uri::create('admin.reference.delete');
		$components = [
			'add_reference' => View_Smarty::forge('admin/components/add_reference.tpl'),
			'edit_reference' => View_Smarty::forge('admin/components/edit_reference.tpl'),
			'delete_reference' => View_Smarty::forge('admin/components/delete_reference.tpl'),
		];
		$this->components = $components;

		// フラッシュ変数を取得
		$this->flash = Session::get_flash($this->flash_name);

		$layout->title = '参照日付一覧';
		$layout->nav_item = 'reference';
		$layout->breadcrumbs = ['一覧' => ''];

		return $layout;
	}
}
