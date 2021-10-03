<?php

/**
 * @package  App\Table
 */

namespace MyApp\Table;

use DB;
use Model_Division;
use Model_Event;
use Model_Event_Detail;

class Event
{
	protected static $table_name_division = 'divisions';
	protected static $table_name_event = 'events';
	protected static $table_name_detail = 'event_details';

	protected static $model_name_division = Model_Division::class;
	protected static $model_name_event = Model_Event::class;
	protected static $model_name_detail = Model_Event_Detail::class;

	public static function get_by_division($divisions, $start_date = null, $end_date = null)
	{
		$query = DB::select('d.*', 'e.title', 'e.date', 'e.comment', 'e.source')
			->from([static::$table_name_detail, 'd'])
			->join([static::$table_name_event, 'e'])
			->on('e.id', '=', 'd.event_id')
			->where('d.is_refer', '=', false)
			->where('e.deleted_at', '=', null)
			->where('d.deleted_at', '=', null);

		if (is_array($divisions) || $divisions instanceof \Fuel\Core\Database_Result_Cached) {
			$ids = [];
			foreach ($divisions as $division) {
				$ids[] = $division->id;
			}
			if ($ids) {
				$query->where('d.division_id', 'in', $ids);
			}
		} else {
			$query->where('d.division_id', '=', $divisions->id);
		}
		if ($start_date) {
			$query->where('e.date', '>=', $start_date);
		}
		if ($end_date) {
			$query->where('e.date', '<=', $end_date);
		}
		$query->order_by('e.date', 'desc');

		return $query->as_object(static::$model_name_detail)->execute()->as_array();
	}
	// function get_by_division()

	public static function get_relative_division(int $event_id)
	{
		$query = DB::select(
			'd.*',
			[DB::expr('concat(d.name, d.suffix)'), 'fullname'],
			['e.id', 'event_detail_id'],
			'e.result',
			'e.geoshape',
			'e.is_refer'
		)
			->from([static::$table_name_detail, 'e'])
			->join([static::$table_name_division, 'd'])
			->on('e.division_id', '=', 'd.id')

			->where('e.deleted_at', '=', null)
			->where('e.event_id', '=', $event_id)
			->order_by('e.order', 'asc');

		return $query->as_object(static::$model_name_division)->execute();
	}
	// function get_relative_division()

	public static function get_by_parent_division_and_date(
		Model_Division $parent,
		string $start_date = null,
		string $end_date = null
	) {
		$query = DB::select('ev.*')
			->distinct(true)
			->from([static::$table_name_detail, 'dt'])
			->join([static::$table_name_event, 'ev'])
			->on('ev.id', '=', 'dt.event_id')
			->join([static::$table_name_division, 'dv'])
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

		return $query->as_object(static::$model_name_event)->execute()->as_array();
	}
}
