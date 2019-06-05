<?php
/**
 * The Admin Base Controller.
 *
 * 管理者画面の基底クラス
 *
 * @package  app
 * @extends  Controller_Base
 */
abstract class Controller_Admin_Base extends Controller_Base
{
	public function before()
	{
		parent::before();

		// 管理者ユーザ情報を取得
		if ( ! $this->_user)
		{
			//throw new HttpNoAccessException;
			// ログインページへリダイレクト
			Helper_Uri::redirect('login');
		}
	} // function before()

	public function after($response)
	{
		$response->description = '管理画面';
		$response->robots = 'noindex,nofollow';
		$response->og_type = 'article';

		return parent::after($response);
	} // function after()
} // class Controller_Admin_Base
