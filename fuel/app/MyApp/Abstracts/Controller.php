<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

use MyApp\Helper\Session\Item as SessionItem;

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
	 * ログインしていない状態でアクセスすると例外を投げる
	 * @throws \HttpNoAccessException  未ログインの場合
	 */
	protected function requireUser(): void
	{
		$user = $this->user();
		if (! $user) {
			throw new \HttpNoAccessException('permission denied');
		}
		$this->user = $user;
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

	/**
	 * リダイレクト
	 * @param  string $config               URL の設定名
	 * @param  array  $params               URL のパラメータ
	 */
	protected function redirect(string $config, array $params = [])
	{
		\Helper_Uri::redirect($config, $params);
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
