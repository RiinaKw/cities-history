<?php

class Presenter_Admin_Db_List extends Presenter_Layout
{
	public function view()
	{
		$this->flash = Session::get_flash($this->flash_name);

		$this->url_do_backup = Helper_Uri::create('admin.db.backup');
		$components = [
			'backup' => View_Smarty::forge('admin/components/db/backup.tpl'),
		];
		$this->components = $components;

		$this->title = 'バックアップ一覧';
		$this->nav_item = 'admin-db';
		$this->breadcrumbs = ['バックアップ' => ''];
		$this->show_share = false;
	} // function view()
} // class Presenter_Admin_Db_Backup
