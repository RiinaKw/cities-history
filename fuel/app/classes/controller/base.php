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
	public function before()
	{
		parent::before();
	} // function before()
} // class Controller_Base
