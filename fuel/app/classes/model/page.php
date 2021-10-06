<?php

/**
 * @package  App\Model
 */
class Model_Page extends \MyApp\Abstracts\ActiveRecord
{
	protected static $_table_name	= 'pages';
	protected static $_primary_key	= ['id'];
	protected static $_created_at	= 'created_at';
	protected static $_updated_at	= 'updated_at';
	protected static $_deleted_at	= 'deleted_at';
	protected static $_mysql_timestamp = true;

	/*
	public function validation($is_new = false, $factory = null)
	{
		$validation = Validation::forge($factory);
		$validation->add_callable(new Helper_MyValidation());

		$arr = explode('.', $factory);
		$id = $arr[1];

		// rules
		$validation->add('slug', 'スラッグ')
			->add_rule('required')
			->add_rule('max_length', 20)
			->add_rule('unique', self::$_table_name . '.slug.' . $id);

		$validation->add('title', 'タイトル')
			->add_rule('required')
			->add_rule('max_length', 256);
		$validation->add('content', '本文')
			;

		return $validation;
	}
	// function validation()
	*/

/*
	public static function get_all()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('slug', 'asc');

		$result = $query->as_object(static::class)->execute();
		return $result->count() ? $result : [];
	}
	// function get_all()

	public static function get_one_by_slug($slug)
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('slug', '=', $slug)
			->where('deleted_at', '=', null)
			;

		$result = $query->as_object(static::class)->execute();
		if ($result->count() == 1) {
			return $result[0];
		} else {
			return null;
		}
	}
	// function get_one_by_slug()
	*/
}
// class Model_Page
