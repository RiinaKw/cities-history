<?php

namespace Fuel\Migrations;

class Add_new_columns_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('new_government_code')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_government_code' => array('constraint' => 7,  'null' => true, 'type' => 'varchar', 'after' => 'end_event_id'),
			));
		}
		if ( ! \DBUtil::field_exists('divisions', array('new_display_order')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_display_order' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'after' => 'new_government_code'),
			));
		}
		if ( ! \DBUtil::field_exists('divisions', array('new_end_date')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_end_date' => array('null' => false, 'type' => 'date', 'default' => '9999-12-31', 'after' => 'new_display_order'),
			));
		}
		if ( ! \DBUtil::field_exists('divisions', array('new_is_unfinished')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_is_unfinished' => array('null' => false, 'type' => 'boolean', 'default' => true, 'after' => 'new_end_date'),
			));
		}
		if ( ! \DBUtil::field_exists('divisions', array('new_is_empty_government_code')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_is_empty_government_code' => array('null' => false, 'type' => 'boolean', 'default' => true, 'after' => 'new_is_unfinished'),
			));
		}
		if ( ! \DBUtil::field_exists('divisions', array('new_is_empty_kana')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_is_empty_kana' => array('null' => false, 'type' => 'boolean', 'default' => true, 'after' => 'new_is_empty_government_code'),
			));
		}
	}

	public function down()
	{
		if ( \DBUtil::field_exists('divisions', array('new_government_code')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_government_code',
			));
		}
		if ( \DBUtil::field_exists('divisions', array('new_display_order')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_display_order',
			));
		}
		if ( \DBUtil::field_exists('divisions', array('new_end_date')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_end_date',
			));
		}
		if ( \DBUtil::field_exists('divisions', array('new_is_unfinished')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_is_unfinished',
			));
		}
		if ( \DBUtil::field_exists('divisions', array('new_is_empty_government_code')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_is_empty_government_code',
			));
		}
		if ( \DBUtil::field_exists('divisions', array('new_is_empty_kana')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_is_empty_kana'
			));
		}
	}
}
