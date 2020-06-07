<?php

class Presenter_Admin_Db_List extends Presenter_Layout
{
	public function view()
	{
		$this->flash = Session::get_flash($this->flash_name);

		$this->title = 'バックアップ一覧';
		$this->nav_item = 'admin-db';
		$this->breadcrumbs = ['バックアップ' => ''];
		$this->show_share = false;
	} // function view()
} // class Presenter_Admin_Db_Backup
