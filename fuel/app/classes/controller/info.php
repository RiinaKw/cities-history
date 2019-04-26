<?php
/**
 * The Info Controller.
 *
 * コントローラの基底クラス
 *
 * @package  app
 * @extends  Controller_Base
 */
abstract class Controller_Info extends Controller_Base
{
	public function action_index()
	{
		phpinfo();
	}
} // class Controller_Layout
