<?php

namespace Fuel\Migrations;

class Add_new_top_parent_division_id_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'new_belongs_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'after' => 'parent_division_id'),
			'new_top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int', 'after' => 'new_belongs_division_id'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'new_belongs_division_id',
			'top_parent_division_id',
		));
	}
}
