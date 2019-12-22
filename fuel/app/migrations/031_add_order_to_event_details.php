<?php

namespace Fuel\Migrations;

class Add_order_to_event_details
{
	public function up()
	{
		\DBUtil::add_fields('event_details', array(
			'order' => array('constraint' => 11,  'null' => false, 'type' => 'int', 'after' => 'result'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('event_details', array(
			'order'
		));
	}
}
