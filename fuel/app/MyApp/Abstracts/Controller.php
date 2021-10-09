<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

use MyApp\Helper\Session\Item as SessionItem;
use MyApp\Helper\Uri;

/**
 * コントローラの基底クラス
 */
abstract class Controller extends \Controller
{
	/**
	 * ログイン状態を保存するセッション
	 * @var \Model_User
	 */
	protected $session_user = null;

	/**
	 * ログインしているユーザを取得
	 * @return \Model_User|null  ユーザオブジェクト、未ログインなら null
	 */
	protected function user(): ?\Model_User
	{
		return $this->session_user->get();
	}

	/**
	 * 活動をログに保存
	 * @param string $target  活動内容
	 * @param int    $id      対象のモデルの ID
	 */
	protected function activity(string $target, int $id): void
	{
		\Model_Activity::insert_log([
			'user_id' => $this->user->id,
			'target' => $target,
			'target_id' => $id,
		]);
	}

	public function before()
	{
		parent::before();

		\Config::load('uri', true);
		\Config::load('common', true);

		$this->session_user = new SessionItem('user');
	}
	// function before()

	public function after($response)
	{
		$response = parent::after($response);
		return $response;
	}
	// function after()
}
// class Controller_Base
