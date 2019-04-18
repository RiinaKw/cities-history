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
		$division = Model_Division::get_by_path($path);

		$events = Model_Event::get_by_division_id($division->id);
		$end = Model_Event::find_by_pk($division->end_event_id);
		array_unshift($events, $end);
		$events[] = Model_Event::find_by_pk($division->start_event_id);

		// ビューを設定
		$content = View_Smarty::forge('city_timeline.tpl');
		$content->path = $path;
		$content->events = $events;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		return $this->_get_view();
	}
}
