<?php

namespace Fuel\Migrations;

class Rename_new_geoshape_and_new_is_refer_in_event_details
{
	public function up()
	{
		\DBUtil::modify_fields('event_details', array(
			'new_is_refer' => array('null' => false, 'type' => 'boolean', 'default' => false, 'name' => 'is_refer'),
			'new_geoshape' => array('constraint' => 200,  'null' => true, 'type' => 'varchar', 'name' => 'geoshape'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('event_details', array(
			'is_refer' => array('null' => false, 'type' => 'boolean', 'default' => false, 'name' => 'new_is_refer'),
			'geoshape' => array('constraint' => 200,  'null' => true, 'type' => 'varchar', 'name' => 'new_geoshape'),
		));
	}
}
