<?php
/**
 * The Event Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Event extends Controller_Layout
{
	public function action_add()
	{
		Debug::dump( Input::post() );exit;
	} // action_add()

	public function action_edit()
	{
		Debug::dump( Input::post() );exit;
	} // action_edit()

	public function action_delete()
	{
		Debug::dump( Input::post() );exit;
	} // action_delete()
} // Controller_Event
