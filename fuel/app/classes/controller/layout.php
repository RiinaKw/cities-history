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

		// レイアウトのテンプレートを設定
		$this->_view = View_Smarty::forge('layout.tpl');
		$this->_set_view_var('root', Helper_Uri::root());
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
