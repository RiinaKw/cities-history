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

	protected static $_has_many = [
		'details' => [
			'key_from' => 'id',
			'model_to' => 'Model_Event_Detail',
			'key_to' => 'event_id',
			'cascade_save' => true,
			'conditions' => [
				'order_by' => [
					'order' => 'asc'
				],
			],
		],
	];

	/**
	 * プレゼンテーションモデルのクラス名
	 * @var string
	 */
	protected const PMODEL_CLASS = \MyApp\PresentationModel\Event::class;

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
}
// class Model_Event
