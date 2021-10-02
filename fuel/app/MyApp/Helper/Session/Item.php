<?php

/**
 * @package  App\Helper
 */

namespace MyApp\Helper\Session;

use Session;

/**
 * 特定のセッションを管理するヘルパークラス
 *
 * FuelPHP の Session と違うところは、キーを後から変更できないという点
 *
 * @package  app\Helper\Session
 */
class Item
{
	/**
	 * 管理するセッション名
	 * @var string
	 */
	protected $key = '';

	/**
	 * セッション名を設定
	 *
	 * @param string $key セッション名
	 */
	public function __construct(string $key)
	{
		$this->key = $key;
	}

	/**
	 * セッション変数を取得
	 *
	 * @return mixed セッション変数
	 */
	public function get()
	{
		return Session::get($this->key);
	}

	/**
	 * セッション変数を登録
	 * @param mixed $value 登録する値
	 */
	public function set($value): void
	{
		Session::set($this->key, $value);
	}

	/**
	 * セッション変数を削除
	 */
	public function delete(): void
	{
		Session::delete($this->key);
	}
}
