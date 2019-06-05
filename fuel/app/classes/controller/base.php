<?php
/**
 * The Base Controller.
 *
 * コントローラの基底クラス
 *
 * @package  app
 * @extends  Controller
 */
abstract class Controller_Base extends Controller
{
	protected $_user = null;

	public function before()
	{
		parent::before();

		Config::load('uri', true);
		Config::load('common', true);

		$user_id = Session::get('user_id');
		$this->_user = Model_User::find_by_pk($user_id);
	} // function before()

	public function after($response)
	{
		$response = parent::after($response);
		return $response;
	} // function after()
} // class Controller_Base
