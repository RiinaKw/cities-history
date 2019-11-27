<?php

class Presenter_Admin_Reference_List extends Presenter_Layout
{
	public function view()
	{
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

		$this->flash = Session::get_flash($this->flash_name);

		$this->title = '参照日付一覧';
		$this->nav_item = 'reference';
		$this->breadcrumbs = ['参照日付一覧' => ''];
	} // function view()
} // class Presenter_Admin_Reference_List
