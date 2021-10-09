<?php

/**
 * @package  Fuel\Model
 */

class Model_Event_Detail extends \MyApp\Abstracts\ActiveRecord
{
	use MyApp\Traits\Model\Presentable;

	protected static $_table_name  = 'event_details';
	protected static $_primary_key = ['id'];

	protected static $_belongs_to = [
		'event',
		'division',
	];

	protected static $pmodel_class = \MyApp\PresentationModel\Event\Detail::class;

	public function validation()
	{
		$validation = Validation::forge(mt_rand());

		// 入力ルール
		$validation->add('date', '日付')
			->add_rule('required')
			->add_rule('valid_date', 'Y-m-d');
		$validation->add('result', 'イベント結果')
			->add_rule('required');

		return $validation;
	}
	// function validation()
}
// class Model_Event_Detail
