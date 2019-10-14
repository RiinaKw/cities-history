<?php
/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  app
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Reference extends Controller_Admin_Base
{
	const SESSION_NAME_FLASH  = 'admin_data.reference';

	public function action_list()
	{
		// create Presenter object
		$content = Presenter::forge(
			'admin/reference/list',
			'view',
			null,
			'admin/admin_reference.tpl'
		);
		$content->flash_name = self::SESSION_NAME_FLASH;

		return $content;
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
			// internal error
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
			// internal error
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
			// internal error
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
