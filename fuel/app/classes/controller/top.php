<?php

use MyApp\Table\Division as DivisionTable;

/**
 * The Top Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Top extends Controller_Base
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge('top', 'view', null, 'top.tpl');
		$content->divisions = DivisionTable::get_top_level();

		return $content;
	}
	// function action_index()
}
// class Controller_Top
