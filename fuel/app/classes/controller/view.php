<?php
/**
 * The View Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_View extends Controller_Layout
{
	public function action_index()
	{
		$path = $this->param('path');
		Debug::dump( Model_Division::get_by_path($path) );
	}
}
