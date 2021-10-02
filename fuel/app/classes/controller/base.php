<?php

/**
 * The Base Controller.
 *
 * Base class for controller.
 *
 * @package  App\Controller
 * @extends  Controller
 */
abstract class Controller_Base extends Controller
{
	protected $_user = null;

	public function user(): ?Model_User
	{
		return Helper_Session::user();
	}

	public function before()
	{
		parent::before();

		Config::load('uri', true);
		Config::load('common', true);
	}
	// function before()

	public function after($response)
	{
		$response = parent::after($response);
		return $response;
	}
	// function after()
}
// class Controller_Base
