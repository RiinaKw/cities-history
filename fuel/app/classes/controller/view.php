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

		$events = Model_Event_Detail::get_by_division_id($division->id);
		foreach ($events as &$event)
		{
			$event->birth = false;
			$event->live = false;
			$event->death = false;
			if ($division->start_event_id == $event->event_id)
			{
				$event->birth = true;
			}
			switch ($event->division_result)
			{
				case '存続':
					$event->live = true;
				break;
				case '廃止':
					$event->death = true;
				break;
			}
			$divisions = Model_Event::get_relative_division($event->event_id);
			if ($divisions)
			{
				foreach ($divisions as &$d)
				{
					$d_path = $d->get_path(null, true);
					$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d_path]);
				}
			}
			$event->divisions = $divisions;
		}

		$breadcrumbs = [];
		$arr = explode('/', $path);
		$cur_path = '';
		foreach ($arr as $name)
		{
			if ($cur_path)
			{
				$cur_path .= '/'.$name;
			}
			else
			{
				$cur_path .= $name;
			}
			if ($cur_path == $path)
			{
				$breadcrumbs[$name] = '';
			}
			else
			{
				$breadcrumbs[$name] = Helper_Uri::create('division.detail', ['path' => $cur_path]);
			}
		}

		// ビューを設定
		$content = View_Smarty::forge('city_timeline.tpl');
		$content->path = $path;
		$content->division = $division;
		$content->events = $events;
		$content->url_detail = Helper_Uri::create('division.detail', ['path' => $path]);
		$content->url_belongto = Helper_Uri::create('division.belongto', ['path' => $path]);
		$content->url_edit = Helper_Uri::create('division.edit', ['path' => $path]);
		$content->url_event_detail = Helper_Uri::create('event.detail');
		$content->url_event_add = Helper_Uri::create('event.add');
		$content->url_event_edit = Helper_Uri::create('event.edit');
		$content->url_event_delete = Helper_Uri::create('event.delete');

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', $path);
		$this->_set_view_var('breadcrumbs', $breadcrumbs);
		return $this->_get_view();
	} // function action_index()

	public function action_belongto()
	{
		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);

		$division_id_arr = Model_Division::get_by_parent_division_id($division->id);

		$events_arr = [];
		if ($division_id_arr)
		{
			$events = Model_Event_Detail::get_by_division_id($division_id_arr);
			foreach ($events as &$event)
			{
				if (isset($events_arr[$event->event_id]))
				{
					continue;
				}
				$event->birth = false;
				$event->live = false;
				$event->death = false;
				if ($division->start_event_id == $event->event_id)
				{
					$event->birth = true;
				}
				switch ($event->division_result)
				{
					case '存続':
						$event->live = true;
					break;
					case '廃止':
						$event->death = true;
					break;
				}
				$divisions = Model_Event::get_relative_division($event->event_id);
				if ($divisions)
				{
					foreach ($divisions as &$d)
					{
						$d_path = $d->get_path(null, true);
						$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d_path]);
					}
				}
				$event->divisions = $divisions;
				$events_arr[$event->event_id] = $event;
			}
		} // if ($division_id_arr)

		$breadcrumbs = [];
		$arr = explode('/', $path);
		$cur_path = '';
		foreach ($arr as $name)
		{
			if ($cur_path)
			{
				$cur_path .= '/'.$name;
			}
			else
			{
				$cur_path .= $name;
			}
			if ($cur_path == $path)
			{
				$breadcrumbs[$name] = '';
			}
			else
			{
				$breadcrumbs[$name] = Helper_Uri::create('division.detail', ['path' => $cur_path]);
			}
		}

		// ビューを設定
		$content = View_Smarty::forge('city_timeline.tpl');
		$content->path = $path;
		$content->division = $division;
		$content->events = $events_arr;
		$content->url_detail = Helper_Uri::create('division.detail', ['path' => $path]);
		$content->url_belongto = Helper_Uri::create('division.belongto', ['path' => $path]);
		$content->url_edit = Helper_Uri::create('division.edit', ['path' => $path]);
		$content->url_event_detail = Helper_Uri::create('event.detail');
		$content->url_event_add = Helper_Uri::create('event.add');
		$content->url_event_edit = Helper_Uri::create('event.edit');
		$content->url_event_delete = Helper_Uri::create('event.delete');

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', $path);
		$this->_set_view_var('breadcrumbs', $breadcrumbs);
		return $this->_get_view();
	} // function action_belongto()

	public function action_edit()
	{
		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);

		$parent = Input::post('parent');
		$parent_division = Model_Division::get_by_path($parent);
		if ( ! $parent_division)
		{
			$parent_division = Model_Division::set_path($parent);
			$parent_division = array_pop($parent_division);
		}
		$division->parent_division_id = $parent_division->id;

		$division->name         = Input::post('name');
		$division->name_kana    = Input::post('name_kana');
		$division->postfix      = Input::post('postfix');
		$division->postfix_kana = Input::post('postfix_kana');
		$division->identify     = Input::post('identify') ?: null;
		$division->save();

		$path_new = $division->get_path(null, true);

		Helper_Uri::redirect('division.detail', ['path' => $path_new]);
		return;
	} // function action_edit()

	public function action_list()
	{
		$divisions = Model_Division::find_all();
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('view.division', ['path' => $division->path]);
		}

		// ビューを設定
		$content = View_Smarty::forge('list.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_list()
} // class Controller_View
