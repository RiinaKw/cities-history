<?php

namespace Fuel\Migrations;

class Delete_old_columns_from_divisions
{
	public function up()
	{
		\DBUtil::drop_index('divisions', 'idx_divisions_top_parent_division_id');
		\DBUtil::drop_fields('divisions', array(
			'government_code',
			'display_order',
			'end_date',
			'is_unfinished',
			'is_empty_government_code',
			'is_empty_kana'
		));
	}

	public function down()
	{
		if ( ! \DBUtil::field_exists('divisions', array('government_code')))
		{
			\DBUtil::add_fields('divisions', array(
				'government_code' => array('constraint' => 7,  'null' => true, 'type' => 'varchar', 'after' => 'end_event_id'),
				'display_order' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
				'end_date' => array('null' => false, 'type' => 'date', 'default' => '9999-12-31'),
				'is_unfinished' => array('null' => false, 'type' => 'boolean', 'default' => true),
				'is_empty_government_code' => array('null' => false, 'type' => 'boolean', 'default' => true),
				'is_empty_kana' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
			\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');
		}
	}
}
