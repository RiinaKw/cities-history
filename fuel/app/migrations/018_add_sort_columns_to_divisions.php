<?php

namespace Fuel\Migrations;

class Add_sort_columns_to_divisions
{
	public function up()
	{
		if (! \DBUtil::field_exists('divisions', array('is_empty_government_code'))) {
			\DBUtil::add_fields('divisions', array(
				'is_empty_government_code' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
			\DBUtil::create_index('divisions', 'is_empty_government_code', 'idx_divisions_is_empty_government_code');
		}
		if (! \DBUtil::field_exists('divisions', array('is_empty_kana'))) {
			\DBUtil::add_fields('divisions', array(
				'is_empty_kana' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
			\DBUtil::create_index('divisions', 'is_empty_kana', 'idx_divisions_is_empty_kana');
		}
		if (! \DBUtil::field_exists('divisions', array('end_date'))) {
			\DBUtil::add_fields('divisions', array(
				'end_date' => array('null' => false, 'type' => 'date', 'default' => '9999-12-31'),
			));
			\DBUtil::create_index('divisions', 'end_date', 'idx_divisions_end_date');
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('is_empty_government_code'))) {
			\DBUtil::drop_fields('divisions', array(
				'is_empty_government_code',
			));
		}
		if (\DBUtil::field_exists('divisions', array('is_empty_kana'))) {
			\DBUtil::drop_fields('divisions', array(
				'is_empty_kana',
			));
		}
		if (\DBUtil::field_exists('divisions', array('end_date'))) {
			\DBUtil::drop_fields('divisions', array(
				'end_date',
			));
		}
	}
}
