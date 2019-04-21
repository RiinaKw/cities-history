<?php

class Model_Event extends Model_Base
{
	protected static $_table_name  = 'events';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation($is_new = false, $factory = null)	// 引数は単なる識別子、何でもいい
	{
		$validation = Validation::forge($factory);

		// 入力ルール
		$field = $validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date', 'Y-m-d');

		return $validation;
	} // function validation()

	public static function create($param)
	{
		$object = self::forge([
			'date' => $param['date'],
			'type' => $param['type']
		]);
		$object->save();
		return $object;
	} // function create()

	public static function get_relative_division($event_id)
	{
		$query = DB::select('d.*', ['e.id', 'event_detail_id'], 'e.division_result')
			->from(['event_details', 'e'])
			->join(['divisions', 'd'])
			->on('e.division_id', '=', 'd.id')
			->where('e.deleted_at', '=', null)
			->where('e.event_id', '=', $event_id);

		return $query->as_object('Model_Division')->execute()->as_array();
	}
} // class Model_Event
