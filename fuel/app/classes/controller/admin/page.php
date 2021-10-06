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
	use ModelRelated;

	protected $session_flash = null;

	protected static function getModelClass(): string
	{
		return Model_Page::class;
	}

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
