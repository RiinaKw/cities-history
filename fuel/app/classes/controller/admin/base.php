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

	public function after($response)
	{
		$this->_set_view_var('description', '管理画面');
		$this->_set_view_var('robots', 'noindex,nofollow');
		$this->_set_view_var('og_type', 'article');
		
		return parent::after($response);
	}
} // class Controller_Admin_Base
