<?php
/**
 * The Add Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Add extends Controller
{
	public function action_index()
	{
		$path = $this->param('path');
		Debug::dump( Model_Division::set_path($path) );
	}
}
