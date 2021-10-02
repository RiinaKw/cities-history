<?php

use MyApp\Table\Division as DivisionTable;
use MyApp\Table\Event as EventTable;

/**
 * The Division Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Division extends Controller_Base
{
	protected function requirePath(): Model_Division
	{
		$path = $this->param('path');
		$division = DivisionTable::get_by_path($path);
		if (! $division || $division->get_path() !== $path || $division->deleted_at !== null) {
			throw new HttpNotFoundException('自治体が見つかりません。');
		}
		return $division;
	}

	protected function events(Model_Division $division): array
	{
		$events = EventTable::get_by_division($division);
		// 終了インベントを先頭に
		foreach ($events as $key => $event) {
			if ($event->event_id === $division->end_event_id) {
				unset($events[$key]);
				array_unshift($events, $event);
				break;
			}
		}
		// 開始イベントを末尾に
		foreach ($events as $key => $event) {
			if ($event->event_id === $division->start_event_id) {
				unset($events[$key]);
				array_push($events, $event);
				break;
			}
		}
		return $events;
	}

	public function action_detail()
	{
		$division = $this->requirePath();
		$events = $this->events($division);

		foreach ($events as $event) {
			$event->birth = false;
			$event->live = false;
			$event->death = false;
			if ($division->start_event_id == $event->event_id) {
				$event->birth = true;
			} elseif ($division->end_event_id == $event->event_id) {
				$event->death = true;
			}
			switch ($event->result) {
				case '存続':
					$event->live = true;
					break;
				case '廃止':
				case '分割廃止':
					$event->death = true;
					break;
			}
			$divisions = EventTable::get_relative_division($event->event_id);
			foreach ($divisions as $d) {
				$d->split = ($d->result == '分割廃止');
				$d->li_class = $d->pmodel()->htmlClass();
			}
			$event->divisions = $divisions;
		}
		// foreach ($events as &$event)

		// create Presenter object
		$content = Presenter::forge('division/detail', 'view', null, 'timeline.tpl');
		$content->current = 'detail';
		$content->path = $division->get_path();
		$content->division = $division;
		$content->belongs_division = $division->belongs();
		$content->events = $events;

		return $content;
	}
	// function action_detail()

	public function action_children()
	{
		$division = $this->requirePath();
		$label = Input::get('label');
		$start = Input::get('start');
		$end = Input::get('end');

		$divisions = DivisionTable::get_by_parent_division_and_date($division);
		$events_arr = [];
		if (count($divisions)) {
			$events = EventTable::get_by_division($divisions, $start, $end);
			foreach ($events as $event) {
				if (isset($events_arr[$event->event_id])) {
					continue;
				}

				$event->birth = false;
				$event->live = false;
				$event->death = false;
				if ($division->start_event_id == $event->event_id) {
					$event->birth = true;
				} elseif ($division->end_event_id == $event->event_id) {
					$event->death = true;
				}
				switch ($event->result) {
					case '存続':
						$event->live = true;
						break;
					case '廃止':
						$event->death = true;
						break;
				}

				$divisions = EventTable::get_relative_division($event->event_id);
				foreach ($divisions as $d) {
					$d->split = ($d->result == '分割廃止');
					$d->li_class = $d->pmodel()->htmlClass();
				}
				$event->divisions = $divisions;
				$events_arr[$event->event_id] = $event;
			}
		}
		// if ($division_id_arr)

		// create Presenter object
		$content = Presenter::forge('division/children', 'view', null, 'timeline.tpl');
		$content->current = $label;
		$content->path = $division->get_path();
		$content->division = $division;
		$content->belongs_division = $division->belongs();
		$content->events = $events_arr;

		return $content;
	}
	// function action_children()

	public function post_add()
	{
		$this->requireUser();

		try {
			DB::start_transaction();

			$division = Model_Division::forge();
			$division->create(Input::post());

			$this->activity('add division', $division->id);

			$path_new = $division->get_path();
			DB::commit_transaction();
		} catch (HttpBadRequestException $e) {
			// internal error
			DB::rollback_transaction();
			throw $e;
		} catch (Exception $e) {
			Debug::dump($e, $e->getTraceAsString());
			//exit;
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}

		$this->redirect('division.detail', ['path' => $path_new]);
		return;
	}
	// function action_add()

	public function post_add_csv()
	{
		$this->requireUser();

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
				$line = trim($line);
				if (! $line) {
					continue;
				}
				$items = explode($separator, $line);
				$arr = [];
				$count = count($heads);
				for ($i = 0; $i < $count; ++$i) {
					$arr[ $heads[$i] ] = trim($items[$i]);
				}
				$arr['parent'] = dirname($arr['path']);

				$divisions = DivisionTable::set_path($arr['path']);
				$division = array_pop($divisions);
				$division->create($arr);
			}

			DB::commit_transaction();

			$this->redirect('division.detail', ['path' => $division->get_path()]);
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			//Debug::dump($e);
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}

	public function action_edit()
	{
		$this->requireUser();

		$input = Input::post();
		$input['is_unfinished'] = isset($input['is_unfinished']) ? $input['is_unfinished'] : false;

		try {
			DB::start_transaction();

			$path = $this->param('path');
			$division = DivisionTable::get_by_path($path);
			$division->create($input);

			$this->activity('edit division', $division->id);

			DB::commit_transaction();

			$path_new = $division->get_path();

			$this->redirect('division.detail', ['path' => $path_new]);
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			//Debug::dump($e);
			throw new HttpServerErrorException($e->getMessage());
		}
		// try
	}
	// function action_edit()

	public function action_delete()
	{
		$this->requireUser();

		$path = $this->param('path');
		$division = DivisionTable::get_by_path($path);
		$division->soft_delete();

		$this->activity('delete division', $division->id);

		if ($division->get_parent_path()) {
			$this->redirect('division.detail', ['path' => $division->get_parent_path()]);
		} else {
			$this->redirect('top');
		}
	}
	// function action_delete()
}
// class Controller_Division
