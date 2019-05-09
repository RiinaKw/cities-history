<?php

class Model_Referencedate extends Model_Base
{
	protected static $_table_name	= 'reference_dates';
	protected static $_primary_key	= 'id';
	protected static $_created_at	= 'created_at';
	protected static $_updated_at	= 'updated_at';
	protected static $_deleted_at	= 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation($is_new = false, $factory = null)	// 引数は単なる識別子、何でもいい
	{
		$validation = Validation::forge($factory);
		$validation->add_callable(new Helper_MyValidation());

		// 入力ルール
		$field = $validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date');

		$field = $validation->add('description', '説明')
			->add_rule('required')
			->add_rule('max_length', 256);

		return $validation;
	} // function validation()

	public static function get_all()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('date', 'desc');

			$result = $query->as_object('Model_Referencedate')->execute()->as_array();
			return $result ?: [];
	} // function get_all()
} // class Model_Referencedate
