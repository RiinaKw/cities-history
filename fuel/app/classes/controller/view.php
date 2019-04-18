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
		$content->url_event_add = Helper_Uri::create('event.add');
		$content->url_event_edit = Helper_Uri::create('event.edit');
		$content->url_event_delete = Helper_Uri::create('event.delete');

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		return $this->_get_view();
	}

	public function action_list()
	{
		$divisions = Model_Division::find_all();
		foreach ($divisions as &$division)
		{
			$division->path = $division->name;
			$parent_id = $division->parent_division_id;
			while($parent_id) {
				$parent = Model_Division::find_by_pk($parent_id);
				$division->path = $parent->name.'/'.$division->path;
				$parent_id = $parent->parent_division_id;
			}
		}

		// ビューを設定
		$content = View_Smarty::forge('list.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		return $this->_get_view();
	}
}
