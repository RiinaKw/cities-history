<?php
/**
 * The About Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_About extends Controller_Base
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge('about', 'view', null, 'about.tpl');

		return $content;
	} // function action_index()
} // class Controller_Top
