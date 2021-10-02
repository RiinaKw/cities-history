<?php

/**
 * @package  App\Model
 */
class Model_Event extends Model_Base
{
	protected static $_table_name  = 'events';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation(): Validation
	{
		$validation = Validation::forge(mt_rand());

		// rules
		$validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date', 'Y-m-d');

		return $validation;
	}
	// function validation()

	public static function create($param): self
	{
		$object = self::forge([
			'date' => $param['date'],
			'title' => $param['title'],
			'comment' => $param['comment'],
			'source' => $param['source'],
		]);
		$object->save();
		return $object;
	}
	// function create()

	public static function get_relative_division($event_id): Fuel\Core\Database_Result_Cached
	{
		$query = DB::select(
			'd.*',
			[DB::expr('concat(d.name, d.suffix)'), 'fullname'],
			['e.id', 'event_detail_id'],
			'e.result',
			'e.geoshape',
			'e.is_refer'
		)
			->from(['event_details', 'e'])
			->join(['divisions', 'd'])
			->on('e.division_id', '=', 'd.id')

			->where('e.deleted_at', '=', null)
			->where('e.event_id', '=', $event_id)
			->order_by('e.order', 'asc');

		return $query->as_object('Model_Division')->execute();
	}
	// function get_relative_division()

	public function delete()
	{
		$detail = Model_Event_Detail::find_by_event_id($this->id);
		foreach ($detail as $d) {
			$d->soft_delete();
		}
		$this->soft_delete();

		Helper_Uri::redirect('top');
	}
	// function delete()
}
// class Model_Event
