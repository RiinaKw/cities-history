<?php
/**
 * The Division Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Division extends Controller_Base
{
	const SESSION_LIST = 'division';

	public function action_detail()
	{
		$path = $this->param('path');
		$division = Model_Division::get_by_path($path);
		if ( ! $division || $division->get_path() != $path || $division->deleted_at != null)
		{
			throw new HttpNotFoundException('自治体が見つかりません。');
		}

		$events = Model_Event_Detail::get_by_division($division);
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
			switch ($event->result)
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
					$d_path = $d->get_path();
					$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d_path]);
					if ($d->geoshape)
					{
						$d->url_geoshape = Helper_Uri::create('geoshape', ['path' => $d->geoshape]);
					}
					else
					{
						$d->url_geoshape = '';
					}
					$d->split = ($d->result == '分割廃止');
					$d->li_class = '';
					switch ($d->result) {
						case '新設':
							$d->li_class = 'birth';
							break;
						case '編入':
							$d->li_class = 'transfer';
							break;
						case '廃止':
						case '分割廃止':
							$d->li_class = 'death';
							break;
					}
				}
			}
			$event->divisions = $divisions;
		} // foreach ($events as &$event)

		$belongs_division = Model_Division::find_by_pk($division->belongs_division_id);

		Session::set(self::SESSION_LIST, Helper_Uri::current());

		// create Presenter object
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
		if ( ! $division || $division->get_path() != $path)
		{
			throw new HttpNotFoundException('自治体が見つかりません。');
		}

		$divisions = Model_Division::get_by_parent_division_and_date($division);
		$events_arr = [];
		if ($divisions)
		{
			$events = Model_Event_Detail::get_by_division($divisions, $start, $end);
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
				switch ($event->result)
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
						$d_path = $d->get_path();
						$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d_path]);
						if ($d->geoshape)
						{
							$d->url_geoshape = Helper_Uri::create('geoshape', ['path' => $d->geoshape]);
						}
						else
						{
							$d->url_geoshape = '';
						}
						$d->split = ($d->result == '分割廃止');
						$d->li_class = '';
						switch ($d->result) {
							case '新設':
								$d->li_class = 'birth';
								break;
							case '編入':
								$d->li_class = 'transfer';
								break;
							case '廃止':
							case '分割廃止':
								$d->li_class = 'death';
								break;
						}
					}
				}
				$event->divisions = $divisions;
				$events_arr[$event->event_id] = $event;
			}
		} // if ($division_id_arr)

		$belongs_division = Model_Division::find_by_pk($division->belongs_division_id);


		Session::set(self::SESSION_LIST, Helper_Uri::current());

		// create Presenter object
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

		$path_new = $division->get_path();

		Helper_Uri::redirect('division.detail', ['path' => $path_new]);
		return;
	} // function action_add()

	public function action_add_csv()
	{
		if ( ! $this->_user)
		{
			throw new HttpNoAccessException('permission denied');
		}
		if ( ! Input::post())
		{
			throw new HttpBadRequestException('post required');
		}

		try {
			DB::start_transaction();

			$separator = Input::post('type') === 'tsv' ? "\t" : ',';
			$body = explode("\n", Input::post('body'));

			$heads = explode($separator, array_shift($body));
			foreach ($heads as &$item) {
				$item = trim($item);
				if ($item === 'code') {
					$item = 'government_code';
				}
			}

			foreach ($body as $line) {
				if (! $line) {
					continue;
				}
				$items = explode($separator, $line);
				$arr = [];
				for ($i = 0; $i < count($heads); ++$i) {
					$arr[ $heads[$i] ] = trim($items[$i]);
				}
				$arr['parent'] = dirname($arr['path']);

				$divisions = Model_Division::set_path($arr['path']);
				$division = array_pop($divisions);
				$division->create($arr);
			}

			DB::commit_transaction();

			Helper_Uri::redirect('division.detail', ['path' => $division->get_path()]);
		}
		catch (Exception $e)
		{
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try
	}

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

		$path_new = $division->get_path();

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
			'user_id' => Session::get('user_id'),
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
