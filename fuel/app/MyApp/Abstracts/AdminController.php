<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

use MyApp\Helper\Uri;

/**
 * 管理画面向けのコントローラの基底クラス
 */
abstract class AdminController extends Controller
{
	public function before()
	{
		parent::before();

		// ログインしていないユーザをログイン画面へリダイレクト
		try {
			$this->requireUser();
		} catch (\HttpNoAccessException $e) {
			// redirect to login form
			Uri::redirect('login');
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
