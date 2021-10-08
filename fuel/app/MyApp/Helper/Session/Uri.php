<?php

namespace MyApp\Helper\Session;

use Response;
use MyApp\Helper\Uri;

/**
 * URI の保存に特化したセッション
 *
 * @package  App\Helper\Session
 */
class Uri extends Item
{
	/**
	 * 現在の URI を登録
	 */
	public function set_uri()
	{
		$this->set(Uri::current());
	}
	// function set()

	/**
	 * 登録された URI へリダイレクト
	 *
	 * @param integer $code レスポンスコード デフォルトは 303
	 */
	public function redirect(int $code = 303): void
	{
		$uri = $this->get();
		$this->delete();
		Response::redirect($uri, 'location', $code);
	}
	// function redirect()
}
