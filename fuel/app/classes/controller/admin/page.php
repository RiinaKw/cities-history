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
