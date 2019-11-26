<?php
/**
 * The Link Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Link extends Controller_Base
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge('link', 'view', null, 'link.tpl');

		return $content;
	} // function action_index()
} // class Controller_Top
