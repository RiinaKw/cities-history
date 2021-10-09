<?php

use MyApp\Table\Event as EventTable;
use MyApp\Traits\Controller\ModelRelated;

/**
 * イベント情報を取得する REST コントローラ
 *
 * @package  Fuel\Controller\Rest
 * @extends  Controller_Rest
 */
class Controller_Rest_Event extends Controller_Rest
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
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound($value, Model_Event $obj = null)
	{
		if ($obj) {
			return "削除済みのイベントです。 {$key} : {$value}";
		} else {
			return "イベントが見つかりません。 {$key} : {$value}";
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
		foreach ($event->details as $details) {
			$response['details'][] = [
				'id'        => $details->id,
				'name'      => $details->division->name,
				'path'      => $details->division->path,
				'result'    => $details->result,
				'birth'     => ($details->division->start_event_id == $event->id),
				'death'     => ($details->division->end_event_id == $event->id),
				'geoshape'  => $details->geoshape,
				'is_refer'  => (int)$details->is_refer,
			];
		}
		return $this->response($response);
	}
	// function get_detail()
}
// class Controller_Rest_Event
