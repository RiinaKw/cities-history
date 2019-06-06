<?php
/**
 * The Division Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Division extends Controller_Base
{
	const SESSION_LIST = 'division';

	public function action_detail()
	{
		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		if ( ! $division || $division->get_path(null, true) != $path)
		{
			throw new HttpNotFoundException('自治体が見つかりません。');
		}

		$events = Model_Event_Detail::get_by_division_id($division->id);
		// 終了インベントを先頭に
		foreach ($events as $key => $event)
		{
			if ($event->event_id == $division->end_event_id)
			{
				unset($events[$key]);
				array_unshift($events, $event);
				break;
			}
		}
		// 開始イベントを末尾に
		foreach ($events as $key => $event)
		{
			if ($event->event_id == $division->start_event_id)
			{
				unset($events[$key]);
				array_push($events, $event);
				break;
			}
		}
		foreach ($events as $event)
		{
			$event->birth = false;
			$event->live = false;
			$event->death = false;
			if ($division->start_event_id == $event->event_id)
			{
				$event->birth = true;
			}
			else if ($division->end_event_id == $event->event_id)
			{
				$event->death = true;
			}
			switch ($event->division_result)
			{
				case '存続':
					$event->live = true;
				break;
				case '廃止':
				case '分割廃止':
					$event->death = true;
				break;
			}
			$divisions = Model_Event::get_relative_division($event->event_id);
			if ($divisions)
			{
				foreach ($divisions as $d)
				{
					$d_path = $d->get_path(null, true);
					$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d_path]);
					if ($d->geoshape)
					{
						$d->url_geoshape = Helper_Uri::create('geoshape', ['path' => $d->geoshape]);
					}
					else
					{
						$d->url_geoshape = '';
					}
					$d->split = ($d->division_result == '分割廃止');
				}
			}
			$event->divisions = $divisions;
		} // foreach ($events as &$event)

		$belongs_division = Model_Division::find_by_pk($division->belongs_division_id);

		Session::set(self::SESSION_LIST, Helper_Uri::current());

		// ビューを設定
		$content = Presenter::forge('division/detail', 'view', null, 'timeline.tpl');
		$content->current = 'detail';
		$content->path = $path;
		$content->division = $division;
		$content->belongs_division = $belongs_division;
		$content->events = $events;

		return $content;
	} // function action_detail()

	public function action_children()
	{
		$path = $this->param('path');
		$label = Input::get('label');
		$start = Input::get('start');
		$end = Input::get('end');
		$division = Model_Division::get_by_path($path);
		if ( ! $division || $division->get_path(null, true) != $path)
		{
			throw new HttpNotFoundException('自治体が見つかりません。');
		}

		$division_id_arr = Model_Division::get_by_parent_division_id_and_date($division->id);

		$events_arr = [];
		if ($division_id_arr)
		{
			$events = Model_Event_Detail::get_by_division_id($division_id_arr, $start, $end);
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
				else if ($division->end_event_id == $event->event_id)
				{
					$event->death = true;
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
						if ($d->geoshape)
						{
							$d->url_geoshape = Helper_Uri::create('geoshape', ['path' => $d->geoshape]);
						}
						else
						{
							$d->url_geoshape = '';
						}
						$d->split = ($d->division_result == '分割廃止');
					}
				}
				$event->divisions = $divisions;
				$events_arr[$event->event_id] = $event;
			}
		} // if ($division_id_arr)

		$belongs_division = Model_Division::find_by_pk($division->belongs_division_id);


		Session::set(self::SESSION_LIST, Helper_Uri::current());

		// ビューを設定
		$content = Presenter::forge('division/children', 'view', null, 'timeline.tpl');
		$content->current = $label;
		$content->path = $path;
		$content->division = $division;
		$content->belongs_division = $belongs_division;
		$content->events = $events_arr;

		return $content;
	} // function action_children()

	public function action_add()
	{
		if ( ! $this->_user)
		{
			throw new HttpNoAccessException('permission denied');
		}
		if ( ! Input::post())
		{
			throw new HttpBadRequestException('post required');
		}

		$division = Model_Division::forge();
		$division->create(Input::post());

		Model_Activity::insert_log([
			'user_id' => Session::get('user_id'),
			'target' => 'add division',
			'target_id' => $division->id,
		]);

		$path_new = $division->get_path(null, true);

		Helper_Uri::redirect('division.detail', ['path' => $path_new]);
		return;
	} // function action_add()

	public function action_edit()
	{
		if ( ! $this->_user)
		{
			throw new HttpNoAccessException('permission denied');
		}

		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		$division->create(Input::post());

		Model_Activity::insert_log([
			'user_id' => Session::get('user_id'),
			'target' => 'edit division',
			'target_id' => $division->id,
		]);

		$path_new = $division->get_path(null, true);

		Helper_Uri::redirect('division.detail', ['path' => $path_new]);
		return;
	} // function action_edit()

	public function action_delete()
	{
		if ( ! $this->_user)
		{
			throw new HttpNoAccessException('permission denied');
		}

		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		$path = $division->get_parent_path();
		$division->soft_delete();

		Model_Activity::insert_log([
			'user_id' => Session::get('user.id'),
			'target' => 'delete division',
			'target_id' => $division->id,
		]);

		if ($path)
		{
			Helper_Uri::redirect('division.detail', ['path' => $path]);
		}
		else
		{
			Helper_Uri::redirect('top');
		}
	} // function action_delete()
} // class Controller_Division
