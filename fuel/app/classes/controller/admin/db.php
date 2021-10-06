<?php

use MyApp\MyFuel;
use MyApp\Model\Backup;
use MyApp\Model\File;
use MyApp\Helper\Session\Flash as FlashSession;

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
	/**
	 * フラッシュセッション
	 * @var \MyApp\Helper\Session\Flash
	 */
	protected $session_flash = null;

	public function before()
	{
		parent::before();

		$this->session_flash = new FlashSession('admin_data.db');
	}

	/**
	 * ファイル名からファイルオブジェクトを取得
	 * @param  string $filename   ファイル名
	 * @return \MyApp\Model\File  ファイルオブジェクト
	 */
	protected function get_file(string $filename): File
	{
		try {
			$path = Backup::correctPath($filename . '.sql');
			return new File($path);
		} catch (\Exception $e) {
			throw new HttpNotFoundException('バックアップファイルが見つかりません。 : ' . $e->getMessage());
		}
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
		$content->flash = $this->session_flash->get();

		return $content;
	}
	// function action_index()

	public function post_backup()
	{
		try {
			$filename = Input::post('filename');
			$path = Backup::correctPath($filename);
			Backup::export($path);

			Model_Activity::insert_log([
				'user_id' => Session::get('user')->id,
				'target' => 'backup db',
				'target_id' => null,
			]);

			$this->session_flash->set([
				'status'  => 'success',
				'message' => 'バックアップに成功しました。',
			]);
			Helper_Uri::redirect('admin.db.list');
		} catch (\Exception $e) {
			$this->session_flash->set([
				'status'  => 'error',
				'message' => $e->getMessage(),
			]);
		}
	}
	// function post_backup()

	public function action_restore($filename)
	{
		try {
			$file = $this->get_file($filename);

			Backup::truncate();
			Backup::import($file->path);

			Model_Activity::insert_log([
				'user_id' => Session::get('user')->id,
				'target' => 'restore db',
				'target_id' => null,
			]);

			$this->session_flash->set([
				'status'  => 'success',
				'message' => '復元に成功しました。',
			]);
			Helper_Uri::redirect('admin.db.list');
		} catch (\Exception $e) {
			$this->session_flash->set([
				'status'  => 'error',
				'message' => $e->getMessage(),
			]);
		}
	}
	// function post_restore()

	public function post_delete($filename)
	{
		$file = $this->get_file($filename);
		$file->delete();

		Model_Activity::insert_log([
			'user_id' => Session::get('user')->id,
			'target' => 'delete backup',
			'target_id' => null,
		]);

		$this->session_flash->set([
			'status'  => 'success',
			'message' => '削除に成功しました。',
		]);
		Helper_Uri::redirect('admin.db.list');
	}
	// function post_delete()

	public function action_download($filename)
	{
		$file = $this->get_file($filename);
		\File::download($file->path, $file->name);
	}
	// function action_download()
}
// class Controller_Admin_Db
