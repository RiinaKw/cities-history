<?php
/**
 * The Error Controller.
 *
 * エラー画面
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Error extends Controller_Layout
{
	private $_view = null;

	public function before()
	{
		parent::before();
	}

	/**
	 * The 400 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_400($e)
	{
		// ビューを設定
		$content = View_Smarty::forge('error/400.tpl');

		$content->message = $e->getMessage();

		$this->_set_view_var('title', '400');
		$this->_set_view_var('content', $content);

		$this->response_status = 400;
		return $this->_get_view();
	} // function action_400()

	/**
	 * The 403 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_403($e)
	{
		// ビューを設定
		$content = View_Smarty::forge('error/403.tpl');

		$content->message = $e->getMessage();

		$this->_set_view_var('title', '403');
		$this->_set_view_var('content', $content);

		$this->response_status = 403;
		return $this->_get_view();
	} // function action_403()

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404($e)
	{
		// ビューを設定
		$content = View_Smarty::forge('error/404.tpl');

		$content->message = $e->getMessage();

		$this->_set_view_var('title', '404');
		$this->_set_view_var('content', $content);

		$this->response_status = 404;
		return $this->_get_view();
	} // function action_404()

	/**
	 * The 500 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_500($e)
	{
		// ビューを設定
		$content = View_Smarty::forge('error/500.tpl');

		$content->message = $e->getMessage();

		$this->_set_view_var('title', '500');
		$this->_set_view_var('content', $content);

		$this->response_status = 500;
		return $this->_get_view();
	} // function action_500()
} // class Controller_Error
