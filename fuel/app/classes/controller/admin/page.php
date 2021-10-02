<?php

/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  App\Controller
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Page extends Controller_Admin_Base
{
	protected const SESSION_NAME_FLASH  = 'admin_data.reference';

	public function action_list()
	{
		// create Presenter object
		$content = Presenter::forge(
			'admin/page/list',
			'view',
			null,
			'admin/admin_page.tpl'
		);
		$content->flash_name = self::SESSION_NAME_FLASH;

		return $content;
	}
	// function action_list()

	protected static function _get_model($id, $force = false)
	{
		if (! $id || ! is_numeric($id)) {
			throw new HttpBadRequestException('不正なIDです。');
		}
		$record = Model_Page::find_by_pk($id);
		if (! $record) {
			throw new HttpNotFoundException('参照が見つかりません。');
		}
		if (! $force && $record->deleted_at) {
			throw new HttpNotFoundException('削除済みです。');
		}
		return $record;
	}
}
// class Controller_Admin
