<?php

namespace Fuel\Migrations;

class Add_new_geoshape_and_new_is_refer_to_event_details
{
	public function up()
	{
		\DBUtil::add_fields('event_details', array(
			'new_is_refer' => array('null' => false, 'type' => 'boolean', 'default' => false, 'after' => 'order'),
			'new_geoshape' => array('constraint' => 200,  'null' => true, 'type' => 'varchar', 'after' => 'new_is_refer'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('event_details', array(
			'new_is_refer',
			'new_geoshape',
		));
	}
}
