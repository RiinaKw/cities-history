<?php
/**
 * The Layout Controller.
 *
 * コントローラの基底クラス
 *
 * @package  app
 * @extends  Controller_Base
 */
abstract class Controller_Layout extends Controller_Base
{
	private $_view = null;

	public function before()
	{
		parent::before();

		$admin_id = Session::get('user.id');
		$this->user = Model_User::find_by_pk($admin_id);

		$q = Input::get('q');

		// レイアウトのテンプレートを設定
		$this->_view = View_Smarty::forge('layout.tpl');
		$this->_set_view_var('root', Helper_Uri::root());
		$this->_set_view_var('user', $this->user);
		$this->_set_view_var('q', $q);
		$this->_set_view_var('url_login', Helper_Uri::create('login'));
		$this->_set_view_var('url_logout', Helper_Uri::create('logout'));
		$this->_set_view_var('url_search', Helper_Uri::create('search'));
		$this->_set_view_var('nav_item', '');
		$this->_set_view_var('url_admin_divisions', Helper_Uri::create('admin.divisions'));
	} // function before()

	protected function _set_view_var($key, $value)
	{
		$this->_view->set_global($key, $value);
	} // function _set_view_var()

	protected function _get_view()
	{
		return $this->_view;
	} // function _get_view()
} // class Controller_Layout
