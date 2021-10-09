<?php

namespace Fuel\Migrations;

class Delete_parent_division_id_from_divisions
{
	public function up()
	{
		if (\DBUtil::field_exists('divisions', array('parent_division_id'))) {
			\DBUtil::drop_fields('divisions', array(
				'parent_division_id',
			));
		}
	}

	public function down()
	{
		if (! \DBUtil::field_exists('divisions', array('parent_division_id'))) {
			\DBUtil::add_fields('divisions', array(
				'parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			));
		}
		\DBUtil::drop_index('divisions', 'idx_divisions_parent_division_id');
		\DBUtil::create_index('divisions', 'parent_division_id', 'idx_divisions_parent_division_id');
	}
}
