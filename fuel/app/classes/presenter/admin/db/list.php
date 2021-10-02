<?php

class Presenter_Admin_Db_List extends Presenter_Layout
{
	public function view()
	{
		$backup_dir = realpath(APPPATH . Config::get('common.backup_dir'));
		$files = File::read_dir($backup_dir);
		foreach ($files as &$file) {
			$name = $file;
			$path = realpath($backup_dir . '/' . $name);
			$size = File::get_size($path);
			$file = [
				'name' => $name,
				'size' => Helper_Number::bytes_format($size),
				'time' => File::get_time($path),
				'url_download' => Helper_Uri::create('admin.db.download', ['file' => $name]),
			];
		}
		usort($files, function ($a, $b) {
			if ($a['time'] == $b['time']) {
				return 0;
			}
			return ($a['time'] > $b['time']) ? -1 : 1;
		});
		$this->files = $files;

		$this->flash = Session::get_flash($this->flash_name);

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
