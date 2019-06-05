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
	/**
	 * The 400 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_400($e)
	{
		// ビューを設定
		$content = Presenter::forge('error', 'view', null, 'error.tpl');

		$code = 400;
		$content->code = $code;
		$content->message = $e->getMessage();

		$this->response_status = $code;
		return $content->view();
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
		$content = Presenter::forge('error', 'view', null, 'error.tpl');

		$code = 403;
		$content->code = $code;
		$content->message = $e->getMessage();

		$this->response_status = $code;
		return $content->view();
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
		$content = Presenter::forge('error', 'view', null, 'error.tpl');

		$code = 404;
		$content->code = $code;
		$content->message = $e->getMessage();

		$this->response_status = $code;
		return $content->view();
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
		$content = Presenter::forge('error', 'view', null, 'error.tpl');

		$code = 500;
		$content->code = $code;
		$content->message = $e->getMessage();

		$this->response_status = $code;
		return $content->view();
	} // function action_500()
} // class Controller_Error
