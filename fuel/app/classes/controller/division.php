<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;
use MyApp\Table\Event as EventTable;
use MyApp\Helper\Session\Url as SessionUrl;

/**
 * The Division Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Division extends Controller
{
	protected $session_url = null;

	public function before()
	{
		parent::before();

		$this->session_url = new SessionUrl('division');
		$this->session_url->set_url();
	}
	// function before()

	protected function requirePath(): Model_Division
	{
		$path = $this->param('path');
		$division = DivisionTable::get_by_path($path);
		if (! $division || $division->deleted_at !== null) {
			throw new HttpNotFoundException('自治体が見つかりません。');
		}
		return $division;
	}

	/**
	 * 自治体に紐づくイベント一覧を取得
	 * @param  Model_Division $division  対象の自治体オブジェクト
	 * @return array<int, Model_Event>   イベントオブジェクトの配列
	 */
	protected function events(Model_Division $division): array
	{
		$details = $division->event_details;

		$events = [];
		foreach ($details as $detail) {
			if ($detail->deleted_at) {
				continue;
			}
			$event = $detail->event;
			if (! $event) {
				continue;
			}
			$events[$event->id] = $event;
		}

		uasort($events, function ($a, $b) {
			return ($a->date < $b->date);
		});
		return $events;
	}

	public function action_detail()
	{
		$division = $this->requirePath();
		$events = $this->events($division);

		// create Presenter object
		$content = Presenter::forge('division/detail', 'view', null, 'timeline.tpl');
		$content->current = 'detail';
		$content->title = $division->getter()->path;
		$content->division = $division;
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

		$events = EventTable::get_by_parent_division_and_date($division, $start, $end);

		// create Presenter object
		//$content = Presenter::forge('division/children', 'view', null, 'timeline.tpl');
		$content = Presenter::forge('division/detail', 'view', null, 'timeline.tpl');
		$content->current = $label;
		$content->title = $division->getter()->path . "の所属自治体タイムライン ({$label})";
		$content->division = $division;
		$content->events = $events;

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
		$division->delete();

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
