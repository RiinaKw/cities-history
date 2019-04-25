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

		$admin_id = Session::get('admin.id');
		$this->admin = Model_Admin::find_by_pk($admin_id);

		// レイアウトのテンプレートを設定
		$this->_view = View_Smarty::forge('layout.tpl');
		$this->_set_view_var('root', Helper_Uri::root());
		$this->_set_view_var('admin', $this->admin);
		$this->_set_view_var('url_login', Helper_Uri::create('login'));
		$this->_set_view_var('url_logout', Helper_Uri::create('logout'));
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
