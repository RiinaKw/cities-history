<?php

namespace Fuel\Migrations;

class Rename_new_columns_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'new_government_code' => array('constraint' => 7,  'null' => true, 'type' => 'varchar', 'name' => 'government_code'),
			'new_display_order' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'display_order'),
			'new_end_date' => array('null' => false, 'type' => 'date', 'default' => '9999-12-31', 'name' => 'end_date'),
			'new_is_unfinished' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'is_unfinished'),
			'new_is_empty_government_code' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'is_empty_government_code'),
			'new_is_empty_kana' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'is_empty_kana'),
		));
		\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');
	}

	public function down()
	{
		\DBUtil::drop_index('divisions', 'idx_divisions_top_parent_division_id');
		\DBUtil::modify_fields('divisions', array(
			'government_code' => array('constraint' => 7,  'null' => true, 'type' => 'varchar', 'name' => 'new_government_code'),
			'display_order' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'new_display_order'),
			'end_date' => array('null' => false, 'type' => 'date', 'default' => '9999-12-31', 'name' => 'new_end_date'),
			'is_unfinished' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'new_is_unfinished'),
			'is_empty_government_code' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'new_is_empty_government_code'),
			'is_empty_kana' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'new_is_empty_kana'),
		));
	}
}
