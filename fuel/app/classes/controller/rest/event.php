<?php

use MyApp\Table\Event as EventTable;

/**
 * イベント情報を取得する REST コントローラ
 *
 * @package  Fuel\Controller\Rest
 * @extends  Controller_Rest
 */
class Controller_Rest_Event extends Controller_Rest
{
	use MyApp\Traits\Controller\ModelRelated;

	/**
	 * 関連するモデルのクラス名
	 * @return string
	 */
	protected static function getModelClass(): string
	{
		return Model_Event::class;
	}

	/**
	 * 関連するモデルで検索に使うカラム名
	 * @return string
	 */
	protected static function getModelKey(): ?string
	{
		return 'id';
	}

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound($value, Model_Event $obj = null)
	{
		if ($obj) {
			return "削除済みのイベントです。 id : {$value}";
		} else {
			return "イベントが見つかりません。 id : {$value}";
		}
	}

	/**
	 * イベント情報と詳細を取得
	 * @param  int                 $id  イベント ID
	 * @return Response
	 */
	public function get_detail(int $id): Response
	{
		try {
			$event = static::getModel($id);
		} catch (HttpNotFoundException $e) {
			return $this->response(
				$e->getMessage(),
				404
			);
		}

		$divisions = EventTable::getRelativeDivision($event->id);
		$response = [
			'event' => [
				'id'      => $event->id,
				'title'   => $event->title,
				'date'    => $event->date,
				'comment' => $event->comment,
				'source'  => $event->source,
			],
			'details' => [],
		];
		foreach ($divisions as $division) {
			$response['details'][] = [
				'id'        => $division->event_detail_id,
				'name'      => $division->name,
				'path'      => $division->path,
				'result'    => $division->result,
				'birth'     => ($division->start_event_id == $event->id),
				'death'     => ($division->end_event_id == $event->id),
				'geoshape'  => $division->geoshape,
				'is_refer'  => (int)$division->is_refer,
			];
		}
		return $this->response($response);
	}
	// function get_detail()
}
// class Controller_Rest_Event
