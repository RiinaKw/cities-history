<?php

/**
 * The Top Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Top extends Controller_Base
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge('top', 'view', null, 'top.tpl');
		$content->divisions = Table_Division::get_top_level();

		return $content;
	}
	// function action_index()
}
// class Controller_Top
