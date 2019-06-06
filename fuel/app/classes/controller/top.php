<?php
/**
 * The Top Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Top extends Controller_Base
{
	public function action_index()
	{
		// ビューを設定
		$content = Presenter::forge('top', 'view', null, 'top.tpl');
		$content->divisions = Model_Division::get_top_level();

		return $content;
	} // function action_index()
} // class Controller_Top
