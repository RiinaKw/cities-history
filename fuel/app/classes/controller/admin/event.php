<?php

use MyApp\Abstracts\AdminController;
use MyApp\Traits\Controller\ModelRelated;
use MyApp\Table\Division as DivisionTable;
use MyApp\Helper\Session\Uri as SessionUri;
use MyApp\Helper\GeoShape;
use MyApp\Helper\Date;

/**
 * The Event Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Admin_Event extends AdminController
{
	use ModelRelated;

	/**
	 * 関連するモデルのクラス名とカラム名
	 * @var array<string, string>
	 */
	protected const MODEL_RELATED = [
		'model' => Model_Event::class,
		'key' => 'id',
	];

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  in                                $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound(int $value, Model_Event $obj = null)
	{
		$key = static::MODEL_RELATED['key'];
		if ($obj) {
			return "削除済みのイベントです。 {$key} : {$value}";
		} else {
			return "イベントが見つかりません。 {$key} : {$value}";
		}
	}

	protected $session_uri = null;

	public function before()
	{
		parent::before();

		$this->session_uri = new SessionUri('division');
	}
	// function before()

	protected function unifyPost(): array
	{
		$arr = [];
		foreach (array_keys(Input::post('id')) as $key) {
			if (! Input::post('division.' . $key)) {
				continue;
			}
			$arr[] = [
				'order'    => Input::post('order.' . $key),
				'id'       => Input::post('id.' . $key),
				'division' => Input::post('division.' . $key),
				'result'   => Input::post('result.' . $key),
				'birth'    => Input::post('birth.' . $key),
				'death'    => Input::post('death.' . $key),
				'delete'   => Input::post('delete.' . $key),
				'geoshape' => GeoShape::unify(Input::post('geoshape.' . $key)),
				'refer'    => Input::post('refer.' . $key),
			];
		}
		return $arr;
	}

	protected function submitDetails(array $item, int $event_id, int $division_id)
	{
		$id = $item['id'];
		$is_new = ($id === 'new');
		if ($item['delete']) {
			if (! $is_new) {
				$detail = Model_Event_Detail::find($id);
				$detail->delete();
			}
		} else {
			if ($is_new) {
				$detail = Model_Event_Detail::forge([
					'order' => $item['order'],
					'event_id' => $event_id,
					'division_id' => $division_id,
					'result' => $item['result'],
					'geoshape' => GeoShape::unify($item['geoshape']),
					'is_refer' => (bool)$item['refer'],
				]);
				$detail->save();
			} else {
				$detail = Model_Event_Detail::find($id);
				$detail->order = $item['order'];
				$detail->result = $item['result'];
				$detail->geoshape = $item['geoshape'];
				$detail->is_refer = (bool)$item['refer'];
				$detail->save();
			}
			// if ($id == 'new')
		}
		// if ($item['delete'])
	}

	public function post_add()
	{
		$post = $this->unifyPost();

		try {
			DB::start_transaction();

			$event = Model_Event::forge([
				'date' => Date::format('Y-m-d', Input::post('date')),
				'title' => DateInput::post('title'),
				'comment' => Input::post('comment'),
				'source' => Input::post('source'),
			]);
			$event->save();

			foreach ($post as $item) {
				$id = $item['id'];
				if (! $id) {
					continue;
				}
				$division = DivisionTable::getOrCreateFromPath($item['division']);

				$item['id'] = 'new';
				$this->submitDetails($item, $event->id, $division->id);

				if ($item['birth']) {
					$division->start_event_id = $event->id;
					$division->save();
				}
				if ($item['death']) {
					$division->end_event_id = $event->id;
					$division->save();
				}
			}
			// foreach ($post as $item)

			$this->activity('add event', $event->id);

			DB::commit_transaction();
		} catch (Exception $e) {
			// internal error
			DB::rollback_transaction();
			//var_dump($e);
			//exit;
			throw new HttpServerErrorException($e->getMessage());
		}
		// try

		$this->session_uri->redirect();
		return;
	}
	// function post_add()

	public function post_edit($event_id)
	{
		$event = $this->getModel($event_id);

		$post = $this->unifyPost();

		try {
			DB::start_transaction();

			$event->date = Date::format('Y-m-d', Input::post('date'));
			$event->title = Input::post('title');
			$event->comment = Input::post('comment');
			$event->source = Input::post('source');
			$event->save();

			foreach ($post as $item) {
				$division = DivisionTable::getOrCreateFromPath($item['division']);

				$this->submitDetails($item, $event->id, $division->id);

				if ($item['birth']) {
					$division->start_event_id = $event->id;
					$division->save();
				}
				if ($item['death']) {
					$division->end_event_id = $event->id;
					$division->end_date = $event->date;
					$division->save();
				} else {
					$division->end_date = '9999-12-31';
					$division->save();
				}
			}
			// foreach ($post as $item)

			$this->activity('edit event', $event->id);

			DB::commit_transaction();

			$this->session_uri->redirect();
		} catch (Exception $e) {
			// internal error
			//Debug::dump($e);exit;
			DB::rollback_transaction();
			throw $e;
		}
		// try

		return;
	}
	// function action_edit()

	public function action_delete($event_id)
	{
		$event = $this->getModel($event_id);

		$event->delete();

		$this->activity('delete event', $event_id);

		Debug::dump($event_id, Input::post());
		//exit;
	}
	// function action_delete()
}
// Controller_Event
