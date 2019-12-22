<?php

namespace Fuel\Migrations;

class Rename_new_top_parent_division_id_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'new_belongs_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'belongs_division_id'),
			'new_top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'top_parent_division_id'),
		));
		\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');
	}

	public function down()
	{
		\DBUtil::drop_index('divisions', 'idx_divisions_top_parent_division_id');
		\DBUtil::modify_fields('divisions', array(
			'belongs_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'new_belongs_division_id'),
			'top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'name' => 'new_top_parent_division_id'),
		));
	}
}
