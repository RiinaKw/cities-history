<?php

namespace Fuel\Migrations;

class Delete_top_parent_division_id_from_divisions
{
	public function up()
	{
		\DBUtil::drop_index('divisions', 'idx_divisions_top_parent_division_id');
		\DBUtil::drop_fields('divisions', array(
			'belongs_division_id',
			'top_parent_division_id'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('divisions', array(
			'belongs_division_id' => array('constraint' => 50,  'null' => true, 'type' => 'varchar'),
			'top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
		));
		\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');
	}
}
