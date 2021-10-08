<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;
use MyApp\Model\Division\Tree;
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
		$division = DivisionTable::findByPath($path);
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
		$content = Presenter::forge('division/detail', 'view', null, 'timeline.tpl');
		$content->current = $label;
		$content->title = $division->getter()->path . "の所属自治体タイムライン ({$label})";
		$content->division = $division;
		$content->events = $events;

		return $content;
	}
	// function action_children()

	public function action_tree()
	{
		$division = $this->requirePath();

		$path = $this->param('path');

		$year = (int)Input::get('year');
		$month = (int)Input::get('month');
		$day = (int)Input::get('day');

		if ($year && $month && $day) {
			$date_str = $year . '-' . $month . '-' . $day;
			$timestamp = strtotime($date_str);
			$date = date('Y-m-d', $timestamp);
			$year = (int)date('Y', $timestamp);
			$month = (int)date('m', $timestamp);
			$day = (int)date('d', $timestamp);
		} else {
			$date = null;
			$year = 0;
			$month = 0;
			$day = 0;
		}

		$tree = Tree::create($division, $date);

		// create Presenter object
		$content = Presenter_Division_Tree::forge();
		$content->date = $date;
		$content->year = $year;
		$content->month = $month;
		$content->day = $day;
		$content->division = $division;
		$content->tree = $tree;
		return $content;
	}
	// function action_tree()
}
// class Controller_Division
