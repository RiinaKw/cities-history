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

		$user = $this->user();
		if (! $user) {
			Uri::redirect('login');
		}
		$this->user = $user;
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
