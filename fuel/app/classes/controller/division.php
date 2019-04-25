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
class Controller_Division extends Controller_Layout
{
	public function action_detail()
	{
		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		if ( ! $division || $division->get_path(null, true) != $path)
		{
			throw new HttpNotFoundException('自治体が見つかりません。');
		}

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
		} // foreach ($events as &$event)

		$breadcrumbs_arr = $this->_breadcrumb_and_kana($path);
		$breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$path_kana = $breadcrumbs_arr['path_kana'];

		// ビューを設定
		$content = View_Smarty::forge('timeline.tpl');
		$content->path = $path;
		$content->division = $division;
		$content->events = $events;
		$content->path_kana = $path_kana;
		$content->url_detail = Helper_Uri::create('division.detail', ['path' => $path]);
		$content->url_belongto = Helper_Uri::create('division.belongto', ['path' => $path]);
		$content->url_edit = Helper_Uri::create('division.edit', ['path' => $path]);
		$content->url_delete = Helper_Uri::create('division.delete', ['path' => $path]);
		$content->url_event_detail = Helper_Uri::create('event.detail');
		$content->url_event_add = Helper_Uri::create('event.add');
		$content->url_event_edit = Helper_Uri::create('event.edit');
		$content->url_event_delete = Helper_Uri::create('event.delete');

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', $path);
		$this->_set_view_var('breadcrumbs', $breadcrumbs);
		return $this->_get_view();
	} // function action_detail()

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

		$breadcrumbs_arr = $this->_breadcrumb_and_kana($path);
		$breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$path_kana = $breadcrumbs_arr['path_kana'];

		// ビューを設定
		$content = View_Smarty::forge('timeline.tpl');
		$content->path = $path;
		$content->division = $division;
		$content->events = $events_arr;
		$content->path_kana = $path_kana;
		$content->url_detail = Helper_Uri::create('division.detail', ['path' => $path]);
		$content->url_belongto = Helper_Uri::create('division.belongto', ['path' => $path]);
		$content->url_edit = Helper_Uri::create('division.edit', ['path' => $path]);
		$content->url_delete = Helper_Uri::create('division.delete', ['path' => $path]);
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
		if ( ! $this->admin)
		{
			throw new HttpNoAccessException("permission denied");
		}

		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);

		$parent = Input::post('parent');
		if ($parent)
		{
			$parent_division = Model_Division::get_by_path($parent);
			if ( ! $parent_division)
			{
				$parent_division = Model_Division::set_path($parent);
				$parent_division = array_pop($parent_division);
			}
			$division->parent_division_id = $parent_division->id;
		}

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

	public function action_delete()
	{
		if ( ! $this->admin)
		{
			throw new HttpNoAccessException("permission denied");
		}

		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		$path = $division->get_parent_path();
		$division->soft_delete();

		if ($path)
		{
			Helper_Uri::redirect('division.detail', ['path' => $path]);
		}
		else
		{
			Helper_Uri::redirect('top');
		}
	}

	protected function _breadcrumb_and_kana($path)
	{
		$breadcrumbs = [
			'一覧' => Helper_Uri::create('list'),
		];
		$arr = explode('/', $path);
		$cur_path = '';
		$cur_kana = '';
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
			$cur_division = Model_Division::get_by_path($cur_path);
			$cur_kana .= ($cur_kana ? '/' : '').$cur_division->name_kana.'・'.$cur_division->postfix_kana;
			if ($cur_path == $path)
			{
				$breadcrumbs[$name] = '';
			}
			else
			{
				$breadcrumbs[$name] = Helper_Uri::create('division.detail', ['path' => $cur_path]);
			}
		} // foreach ($arr as $name)

		return [
			'breadcrumbs' => $breadcrumbs,
			'path_kana' => $cur_kana,
		];
	} // function _breadcrumb_and_kana()
} // class Controller_View
