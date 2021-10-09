<?php

use MyApp\Abstracts\AdminController;
use MyApp\Traits\Controller\ModelRelated;
use MyApp\Helper\Session\Flash as FlashSession;

/**
 * The Admin Controller.
 *
 * Admin controller for edit date references.
 *
 * @package  Fuel\Controller
 * @extends  Controller_Admin_Base
 */
class Controller_Admin_Page extends AdminController
{
	use ModelRelated;

	/**
	 * 関連するモデルのクラス名とカラム名
	 * @var array<string, string>
	 */
	protected const MODEL_RELATED = [
		'model' => Model_Page::class,
		'key' => 'id',
	];

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound($value, Model_Page $obj = null)
	{
		if ($obj) {
			return "削除済みのページです。 slug : {$value}";
		} else {
			return "ページが見つかりません。 slug : {$value}";
		}
	}

	protected $session_flash = null;

	public function before()
	{
		parent::before();

		$this->session_flash = new FlashSession('admin_data.page');
	}

	public function action_list()
	{
		// create Presenter object
		$content = Presenter::forge(
			'admin/page/list',
			'view',
			null,
			'admin/admin_page.tpl'
		);
		$content->flash = $this->session_flash->get();

		return $content;
	}
	// function action_list()
}
// class Controller_Admin
