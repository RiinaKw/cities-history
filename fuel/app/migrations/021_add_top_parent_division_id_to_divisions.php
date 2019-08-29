<?php

namespace Fuel\Migrations;

class Add_top_parent_division_id_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('top_parent_division_id')))
		{
			\DBUtil::add_fields('divisions', array(
				'top_parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			));
		}
		\DBUtil::create_index('divisions', 'top_parent_division_id', 'idx_divisions_top_parent_division_id');
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('top_parent_division_id')))
		{
			\DBUtil::drop_fields('divisions', array(
				'top_parent_division_id',
			));
		}
	}
}
