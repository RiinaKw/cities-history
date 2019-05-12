<?php

namespace Fuel\Migrations;

class Add_no_to_event_details
{
	public function up()
	{
		\DBUtil::add_fields('event_details', array(
			'no'        => array('constraint' => 11,  'null' => false, 'type' => 'int'),
		));
		\DBUtil::create_index('event_details', 'no', 'idx_event_details_no');
	}

	public function down()
	{
		\DBUtil::drop_fields('event_details', array(
			'no'
		));
	}
}
