<?php
/**
 * The Admin Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Admin_Reference extends Controller_Admin_Base
{
	const SESSION_NAME_FLASH  = 'admin_data.reference';

	public function action_list()
	{
		// フラッシュ変数を取得
		$flash = Session::get_flash(self::SESSION_NAME_FLASH);

		// ビューを設定
		$content = View_Smarty::forge('admin/admin_reference.tpl');
		$content->dates = Model_Referencedate::get_all();

		$content->url_add    = Helper_Uri::create('admin.reference.add');
		$content->url_edit   = Helper_Uri::create('admin.reference.edit');
		$content->url_delete = Helper_Uri::create('admin.reference.delete');
		$components = [
			'add_reference' => View_Smarty::forge('admin/components/add_reference.tpl'),
			'edit_reference' => View_Smarty::forge('admin/components/edit_reference.tpl'),
			'delete_reference' => View_Smarty::forge('admin/components/delete_reference.tpl'),
		];
		$content->components = $components;

		$content->flash = $flash;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '参照日付一覧');
		$this->_set_view_var('nav_item', 'reference');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_list()

	public function action_add()
	{
		Debug::dump( Input::post() );
		try
		{
			DB::start_transaction();

			$reference = Model_Referencedate::forge([
				'date' => Input::post('date'),
				'description' => Input::post('description'),
			]);
			$reference->save();

			DB::commit_transaction();

			Session::set_flash(
				self::SESSION_NAME_FLASH,
				[
					'status'  => 'success',
					'message' => '追加に成功しました。',
				]
			);
			Helper_Uri::redirect('admin.reference.list');
		}
		catch (Exception $e)
		{
			// 内部エラー
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try
	} // function action_add()

	public function action_edit($id)
	{
		$reference = static::_get_model($id);
		try
		{
			DB::start_transaction();

			$reference->date = Input::post('date');
			$reference->description = Input::post('description');
			$reference->save();

			Session::set_flash(
				self::SESSION_NAME_FLASH,
				[
					'status'  => 'success',
					'message' => '更新に成功しました。',
				]
			);
			DB::commit_transaction();

			Helper_Uri::redirect('admin.reference.list');
		}
		catch (Exception $e)
		{
			// 内部エラー
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try
	} // function action_edit()

	public function action_delete($id)
	{
		$reference = static::_get_model($id);
		try
		{
			DB::start_transaction();

			$reference->soft_delete();

			DB::commit_transaction();

			Session::set_flash(
				self::SESSION_NAME_FLASH,
				[
					'status'  => 'success',
					'message' => '削除に成功しました。',
				]
			);
			Helper_Uri::redirect('admin.reference.list');
		}
		catch (Exception $e)
		{
			// 内部エラー
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try
	} // function action_delete()

	protected static function _get_model($id, $force = false)
	{
		if ( ! $id || ! is_numeric($id))
		{
			throw new HttpBadRequestException('不正なIDです。');
		}
		$record = Model_Referencedate::find_by_pk($id);
		if ( ! $record)
		{
			throw new HttpNotFoundException('参照が見つかりません。');
		}
		if ( ! $force && $record->deleted_at)
		{
			throw new HttpNotFoundException('削除済みです。');
		}
		return $record;
	}
} // class Controller_Admin
