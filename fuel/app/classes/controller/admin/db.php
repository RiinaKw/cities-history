<?php

/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  App\Controller
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Db extends Controller_Admin_Base
{
	protected const SESSION_NAME_FLASH  = 'admin_data.db';

	protected function get_file($filename)
	{
		$backup_dir = realpath(APPPATH . Config::get('common.backup_dir'));
		$ext_arr = ['sql', 'dump'];
		foreach ($ext_arr as $ext) {
			$file = $filename . '.' . $ext;
			$path = $backup_dir . '/' . $file;
			if (File::exists($path)) {
				return $path;
			}
		}
		return null;
	}
	// function get_file()

	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge(
			'admin/db/list',
			'view',
			null,
			'admin/admin_db_list.tpl'
		);
		$content->flash_name = self::SESSION_NAME_FLASH;

		return $content;
	}
	// function action_index()

	public function post_backup()
	{
		$filename = Input::post('filename');
		if (! $filename) {
			$filename = date('YmdHis') . '_from_web.sql';
		}
		$oil_path = realpath(APPPATH . '/../../oil');
		$command = "php {$oil_path} r db:backup --without=users,migration {$filename}";
		if (\Fuel::$env == 'staging') {
			$command = 'FUEL_ENV=staging ' . $command;
		}
		$output = exec($command);

		if (strpos($output, 'Error') === false) {
			Model_Activity::insert_log([
				'user_id' => Session::get('user_id'),
				'target' => 'backup db',
				'target_id' => null,
			]);

			Session::set_flash(
				self::SESSION_NAME_FLASH,
				[
					'status'  => 'success',
					'message' => 'バックアップに成功しました。',
				]
			);
		} else {
			Session::set_flash(
				self::SESSION_NAME_FLASH,
				[
					'status'  => 'error',
					'message' => $output,
				]
			);
		}
		Helper_Uri::redirect('admin.db.list');
	}
	// function post_backup()

	public function action_restore($filename)
	{
		$path = $this->get_file($filename);
		if (! $path) {
			throw new HttpNotFoundException('バックアップファイルが見つかりません。');
		}

		$file = basename($path);

		$oil_path = realpath(APPPATH . '/../../oil');
		$command = "php {$oil_path} r db:restore --without=users,migration {$file}";
		if (\Fuel::$env == 'staging') {
			$command = 'FUEL_ENV=staging ' . $command;
		}
		exec($command);

		Model_Activity::insert_log([
			'user_id' => Session::get('user_id'),
			'target' => 'restore db',
			'target_id' => null,
		]);

		Session::set_flash(
			self::SESSION_NAME_FLASH,
			[
				'status'  => 'success',
				'message' => '復元に成功しました。',
			]
		);
		Helper_Uri::redirect('admin.db.list');
	}
	// function post_restore()

	public function post_delete($filename)
	{
		$path = $this->get_file($filename);
		if (! $path) {
			throw new HttpNotFoundException('バックアップファイルが見つかりません。');
		}

		if ($path) {
			unlink($path);

			Model_Activity::insert_log([
				'user_id' => Session::get('user_id'),
				'target' => 'delete backup',
				'target_id' => null,
			]);
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
	// function post_delete()

	public function action_download($filename)
	{
		$path = $this->get_file($filename);
		if (! $path) {
			throw new HttpNotFoundException('バックアップファイルが見つかりません。');
		}

		File::download($path, basename($path));
	}
	// function action_download()
}
// class Controller_Admin_Db
