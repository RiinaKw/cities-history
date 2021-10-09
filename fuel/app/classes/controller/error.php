<?php

/**
 * The Error Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */

use MyApp\Abstracts\Controller;

class Controller_Error extends Controller
{
	protected function render($e, $code)
	{
		$content = Presenter_Error::forge();

		$content->code = $code;
		$content->message = $e->getMessage();

		$this->response_status = $code;
		return $content;
	}
	// function render($code)

	/**
	 * The 400 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_400($e)
	{
		return $this->render($e, 400);
	}
	// function action_400()

	/**
	 * The 403 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_403($e)
	{
		return $this->render($e, 403);
	}
	// function action_403()

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404($e)
	{
		return $this->render($e, 404);
	}
	// function action_404()

	/**
	 * The 500 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_500($e)
	{
		return $this->render($e, 500);
	}
	// function action_500()
}
// class Controller_Error
