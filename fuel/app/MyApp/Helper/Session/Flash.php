<?php

namespace MyApp\Helper\Session;

use Session;

/**
 * 特定のセッションを管理するヘルパークラス
 *
 * FuelPHP の Session と違うところは、キーを後から変更できないという点
 *
 * @package  App\Helper\Session
 */
class Flash
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
		return Session::get_flash($this->key);
	}

	/**
	 * セッション変数を登録
	 * @param mixed $value 登録する値
	 */
	public function set($value): void
	{
		Session::set_flash($this->key, $value);
	}

	/**
	 * セッション変数を削除
	 */
	public function delete(): void
	{
		Session::delete_flash($this->key);
	}
}
