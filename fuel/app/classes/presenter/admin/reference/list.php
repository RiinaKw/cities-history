<?php

/**
 * @package  App\Presenter
 */
class Presenter_Admin_Reference_List extends Presenter_Layout
{
	public function view()
	{
		$this->dates = Model_Referencedate::get_all();

		$components = [
			'add_reference' => View_Smarty::forge('admin/components/add_reference.tpl'),
			'edit_reference' => View_Smarty::forge('admin/components/edit_reference.tpl'),
			'delete_reference' => View_Smarty::forge('admin/components/delete_reference.tpl'),
		];
		$this->components = $components;

		$this->title = '参照日付一覧';
		$this->nav_item = 'admin-reference';
		$this->breadcrumbs = \MyApp\Helper\Breadcrumb::forge('参照日付一覧');
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Admin_Reference_List
