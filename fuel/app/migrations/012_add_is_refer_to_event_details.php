<?php

namespace Fuel\Migrations;

class Add_is_refer_to_event_details
{
	public function up()
	{
		\DBUtil::add_fields('event_details', array(
			'is_refer' => array('null' => false, 'type' => 'boolean', 'default' => false),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('event_details', array(
			'is_refer'
		));
	}
}
