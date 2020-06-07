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
				'size' => Helper_Number::bytes_format($size),
				'time' => File::get_time($path),
			];
		}
		usort($files, function($a, $b){
			if ($a['time'] == $b['time']) {
				return 0;
			}
			return ($a['time'] > $b['time']) ? -1 : 1;
		});

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
	} // function action_index()

	public function post_backup()
	{
		$filename = Input::post('filename');
		if (! $filename) {
			$filename = date('YmdHis') . '_from_web.sql';
		}
		$oil_path = realpath(APPPATH . '/../../oil');
		$output = exec("php {$oil_path} r db:backup {$filename}");

		Session::set_flash(
			self::SESSION_NAME_FLASH,
			[
				'status'  => 'success',
				'message' => 'バックアップに成功しました。',
			]
		);
		Helper_Uri::redirect('admin.db.list');
	} // function post_backup()

	public function post_delete($filename)
	{
		$backup_dir = realpath(APPPATH . Config::get('common.backup_dir'));
		$ext_arr = ['sql', 'dump'];
		$found = '';
		foreach ($ext_arr as $ext) {
			$file = $filename . '.' . $ext;
			$path = $backup_dir . '/' . $file;
			if ( File::exists($path) ) {
				$found = $path;
				break;
			}
		}

		if ($found) {
			unlink($found);
		}

		Session::set_flash(
			self::SESSION_NAME_FLASH,
			[
				'status'  => 'success',
				'message' => '削除に成功しました。',
			]
		);
		Helper_Uri::redirect('admin.db.list');
	}
} // class Controller_Admin_Db
