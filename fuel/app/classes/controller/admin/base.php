<?php

/**
 * The Admin Base Controller.
 *
 * Base of admin controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
abstract class Controller_Admin_Base extends Controller_Base
{
	public function before()
	{
		parent::before();

		try {
			$this->requireUser();
		} catch (HttpNoAccessException $e) {
			// redirect to login form
			Helper_Uri::redirect('login');
		}
	}
	// function before()

	public function after($response)
	{
		$response->description = '管理画面';
		$response->robots = 'noindex,nofollow';
		$response->og_type = 'article';

		return parent::after($response);
	}
	// function after()
}
// class Controller_Admin_Base
