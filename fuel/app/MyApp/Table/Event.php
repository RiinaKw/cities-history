<?php

/**
 * @package  App\Table
 */

namespace MyApp\Table;

use Fuel\Core\Database_Result_Cached as Result;
use DB;
use Model_Division;
use Model_Event;
use Model_Event_Detail;

/**
 * イベントテーブルを管理するクラス
 */
class Event extends \MyApp\Abstracts\Table
{
	public const TABLE_NAME = 'events';
	public const TABLE_PK = ['id'];

	//================ 各種テーブル名 ================//

	/**
	 * イベントテーブルのテーブル名（つまり自分自身）
	 * @var string
	 */
	protected const EVENT_TABLE    = self::TABLE_NAME;

	/**
	 * 自治体テーブルのテーブル名
	 * @var string
	 */
	protected const DIVISION_TABLE = \MyApp\Table\Division::TABLE_NAME;

	/**
	 * イベント詳細テーブルのテーブル名
	 * @var string
	 */
	protected const DETAIL_TABLE   = \MyApp\Table\Event_detail::TABLE_NAME;

	//================ 各種モデル名 ================//

	/**
	 * イベントモデルのクラス名
	 * @var string
	 */
	protected static $model_name_event    = Model_Event::class;

	/**
	 * 自治体モデルのクラス名
	 * @var string
	 */
	protected static $model_name_division = Model_Division::class;

	/**
	 * イベント詳細モデルのクラス名
	 * @var string
	 */
	protected static $model_name_detail   = Model_Event_Detail::class;

	//================ 各種メソッド ================//

	/**
	 * イベントに関連する自治体一覧を取得
	 *
	 * @param  int    $event_id                   イベント ID
	 * @return \Fuel\Core\Database_Result_Cached  Fuel のデータベースキャッシュ
	 */
	public static function getRelativeDivision(int $event_id): Result
	{
		$query = DB::select(
			'd.*',
			[DB::expr('concat(d.name, d.suffix)'), 'fullname'],
			['e.id', 'event_detail_id'],
			'e.result',
			'e.geoshape',
			'e.is_refer'
		)
			->from([static::DETAIL_TABLE, 'e'])
			->join([static::DIVISION_TABLE, 'd'])
			->on('e.division_id', '=', 'd.id')

			->where('e.deleted_at', '=', null)
			->where('e.event_id', '=', $event_id)
			->order_by('e.order', 'asc');

		return $query->as_object(static::$model_name_division)->execute();
	}
	// function getRelativeDivision()

	/**
	 * 親自治体と期間から、配下の自治体のイベント一覧を取得
	 *
	 * @param  Model_Division $parent             基準となる親自治体
	 * @param  string|null    $start_date         集計開始日付、null の場合は指定なし
	 * @param  string|null    $end_date           集計終了日付、null の場合は指定なし
	 * @return \Fuel\Core\Database_Result_Cached  Fuel のデータベースキャッシュ
	 */
	public static function getByParentStartEnd(
		Model_Division $parent,
		string $start_date = null,
		string $end_date = null
	): Result {
		$query = DB::select('ev.*')
			->distinct(true)
			->from([static::DETAIL_TABLE, 'dt'])
			->join([static::EVENT_TABLE, 'ev'])
			->on('ev.id', '=', 'dt.event_id')
			->join([static::DIVISION_TABLE, 'dv'])
			->on('dv.id', '=', 'dt.division_id')
			->where('dt.is_refer', '=', false)
			->where('ev.deleted_at', '=', null)
			->where('dt.deleted_at', '=', null)
			->where('dv.id_path', 'LIKE', DB::expr('CONCAT("' . $parent->id_path . '", "_%")'));

		if ($start_date) {
			$query->where('ev.date', '>=', $start_date);
		}
		if ($end_date) {
			$query->where('ev.date', '<=', $end_date);
		}
		$query->order_by('ev.date', 'desc');

		return $query->as_object(static::$model_name_event)->execute();
	}
	// function getByParentStartEnd()
}
// class Event
