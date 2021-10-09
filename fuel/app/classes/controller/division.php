<?php

use MyApp\Abstracts\Controller;
use MyApp\Traits\Controller\ModelRelated;
use MyApp\Table\Division as DivisionTable;
use MyApp\Model\Division\Tree;
use MyApp\Table\Event as EventTable;
use MyApp\Helper\Session\Uri as SessionUri;

/**
 * The Division Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Division extends Controller
{
	use ModelRelated;

	/**
	 * 関連するモデルのクラス名とカラム名
	 * @var array<string, string>
	 */
	protected const MODEL_RELATED = [
		'model' => Model_Division::class,
		'key' => 'path',
	];

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */

	protected static function notFound($value, Model_Division $obj = null)
	{
		$key = static::MODEL_RELATED['key'];
		if ($obj) {
			return "削除済みのページです。 {$key} : {$value}";
		} else {
			return "ページが見つかりません。 {$key} : {$value}";
		}
	}

	protected $session_uri = null;

	public function before()
	{
		parent::before();

		$this->session_uri = new SessionUri('division');
		$this->session_uri->set_uri();
	}
	// function before()

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

	public function action_detail($test = null)
	{
		$division = static::getModel($this->param('path'));
		$events = $this->events($division);

		// create Presenter object
		$content = Presenter_Division_Timeline::forge();
		$content->current = 'detail';
		$content->title = $division->getter()->path;
		$content->division = $division;
		$content->events = $events;

		return $content;
	}
	// function action_detail()

	public function action_children()
	{
		$division = static::getModel($this->param('path'));
		$label = Input::get('label');
		$start = Input::get('start');
		$end = Input::get('end');

		$events = EventTable::getByParentStartEnd($division, $start, $end);

		// create Presenter object
		$content = Presenter_Division_Timeline::forge();
		$content->current = $label;
		$content->title = $division->getter()->path . "の所属自治体タイムライン ({$label})";
		$content->division = $division;
		$content->events = $events;

		return $content;
	}
	// function action_children()

	public function action_tree()
	{
		$division = static::getModel($this->param('path'));

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
		$content->tree = $tree;
		return $content;
	}
	// function action_tree()
}
// class Controller_Division
