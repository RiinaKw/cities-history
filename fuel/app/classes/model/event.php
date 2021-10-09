<?php

/**
 * @package  Fuel\Model
 */

use MyApp\Table\Event as Table;

class Model_Event extends \MyApp\Abstracts\ActiveRecord
{
	use MyApp\Traits\Model\Presentable;

	protected static $_table_name  = Table::TABLE_NAME;
	protected static $_primary_key = Table::TABLE_PK;

	protected static $_has_many = ['event_details'];

	protected static $pmodel_class = \MyApp\PresentationModel\Event::class;

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

	public static function createEvent($param): self
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

	/**
	 * @todo なぜこんなところにリダイレクトがある！？
	 */
	public function deleteEvent()
	{
		$detail = Model_Event_Detail::find_by_event_id($this->id);
		foreach ($detail as $d) {
			$d->delete();
		}
		$this->delete();

		\MyApp\Helper\Uri::redirect('top');
	}
	// function delete()
}
// class Model_Event
