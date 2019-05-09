<?php
/**
 * The Admin Base Controller.
 *
 * 管理者画面の基底クラス
 *
 * @package  app
 * @extends  Controller_Base
 */
abstract class Controller_Admin_Base extends Controller_Layout
{
	private $_view = null;

	public function before()
	{
		parent::before();

		// 管理者ユーザ情報を取得
		if ( ! $this->user)
		{
			//throw new HttpNoAccessException;
			// ログインページへリダイレクト
			Helper_Uri::redirect('login');
		}
	}
} // class Controller_Admin_Base
