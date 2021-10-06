<?php

use MyApp\Model\Backup;
use MyApp\Model\File;

/**
 * @package  App\Presenter
 */
class Presenter_Admin_Db_List extends Presenter_Layout
{
	public function view()
	{
		$this->files = Backup::files();

		$this->url_backup = Helper_Uri::create('admin.db.backup');
		$this->url_download = Helper_Uri::create('admin.db.download');
		$this->url_restore = Helper_Uri::create('admin.db.restore');
		$this->url_delete = Helper_Uri::create('admin.db.delete');
		$components = [
			'detail' => View_Smarty::forge('admin/components/db/detail.tpl'),
			'backup' => View_Smarty::forge('admin/components/db/backup.tpl'),
			'restore' => View_Smarty::forge('admin/components/db/restore.tpl'),
			'delete' => View_Smarty::forge('admin/components/db/delete.tpl'),
		];
		$this->components = $components;

		$this->title = 'バックアップ一覧';
		$this->nav_item = 'admin-db';
		$this->breadcrumbs = ['バックアップ' => ''];
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Admin_Db_Backup
