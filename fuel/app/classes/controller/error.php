<?php
/**
 * The Error Controller.
 *
 * エラー画面
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Error extends Controller_Base
{
	public function before()
	{
		parent::before();
	} // function before()

	public function after($response)
	{
		$this->_view->description = '';
		$this->_view->robots = 'noindex,nofollow';
		$this->_view->og_type = '';

		return parent::after($response);
	} // function after()

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

		$this->_view->content = $content;
		$this->_view->title = '400';

		$this->response_status = 400;
		return $this->_view;
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

		$this->_view->content = $content;
		$this->_view->title = '403';

		$this->response_status = 403;
		return $this->_view;
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

		$this->_view->content = $content;
		$this->_view->title = '404';

		$this->response_status = 404;
		return $this->_view;
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

		$this->_view->content = $content;
		$this->_view->title = '500';

		$this->response_status = 500;
		return $this->_view;
	} // function action_500()
} // class Controller_Error
