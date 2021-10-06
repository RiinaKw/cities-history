<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;

/**
 * The Top Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Top extends Controller
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter::forge('top', 'view', null, 'top.tpl');
		$content->divisions = DivisionTable::get_top_level();

		return $content;
	}
	// function action_index()

	public function action_session_clear()
	{
		$division = Session::get('division');
		Session::destroy();
		Session::set('division', $division);

		Response::redirect($division);
	}
}
// class Controller_Top
