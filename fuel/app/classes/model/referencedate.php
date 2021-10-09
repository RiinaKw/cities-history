<?php

/**
 * @package  Fuel\Model
 */
class Model_Referencedate extends \MyApp\Abstracts\ActiveRecord
{
	protected static $_table_name	= 'reference_dates';
	protected static $_primary_key	= ['id'];

	public function validation()
	{
		$validation = Validation::forge(mt_rand());
		$validation->add_callable(new Helper_MyValidation());

		// rules
		$validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date');

		$validation->add('description', '説明')
			->add_rule('required')
			->add_rule('max_length', 256);

		return $validation;
	}
	// function validation()

	public static function get_all()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('date', 'desc');

			$result = $query->as_object('Model_Referencedate')->execute();
			return $result->count() ? $result->as_array() : [];
	}
	// function get_all()
}
// class Model_Referencedate
