<?php

/**
 * @package  App\Helper
 */

namespace MyApp\Helper\Session;

use Response;
use Helper_Uri;

class Url extends Item
{
	/**
	 * 現在の URL を登録
	 */
	public function set_url()
	{
		$this->set(Helper_Uri::current());
	} // function set()

	/**
	 * 登録された URL へリダイレクト
	 *
	 * @param integer $code レスポンスコード デフォルトは 303
	 */
	public function redirect(int $code = 303): void
	{
		$url = $this->get();
		$this->delete();
		Response::redirect($url, 'location', $code);
	} // function redirect()
}
