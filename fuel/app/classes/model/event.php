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

	public static function get_by_division_id($division_id)
	{
		$query = DB::select()
			->from([self::$_table_name, 'e'])
			->join(['event_details', 'd'])
			->on('e.id', '=', 'd.event_id')
			->where('d.division_id', '=', $division_id)
			->order_by('e.date', 'desc');

		return $query->as_object('Model_Event')->execute()->as_array();
	} // function get_by_division_id()
} // class Model_Event
