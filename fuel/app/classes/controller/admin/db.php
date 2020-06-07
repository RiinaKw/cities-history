<?php
/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  app
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Db extends Controller_Admin_Base
{
	const SESSION_NAME_FLASH  = 'admin_data.db';

	public function action_index()
	{
		$backup_dir = realpath(APPPATH . Config::get('common.backup_dir'));
		$files = File::read_dir($backup_dir);
		foreach ($files as &$file) {
			$name = $file;
			$path = realpath($backup_dir . '/' . $name);
			$size = File::get_size($path);
			$file = [
				'name' => $name,
				'size' => $size,
				'time' => File::get_time($path),
			];
		}

		// create Presenter object
		$content = Presenter::forge(
			'admin/db/list',
			'view',
			null,
			'admin/admin_db_list.tpl'
		);
		$content->files = $files;
		$content->flash_name = self::SESSION_NAME_FLASH;

		return $content;
	} // function action_list()

	public function post_backup()
	{
		$oil_path = realpath(APPPATH . '/../../oil');
		$output = exec("php {$oil_path} r db:backup");

		Session::set_flash(
			self::SESSION_NAME_FLASH,
			[
				'status'  => 'success',
				'message' => 'バックアップに成功しました。',
			]
		);
		Helper_Uri::redirect('admin.db.list');
	} // action_backup()
} // class Controller_Admin_Db
