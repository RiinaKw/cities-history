<?php

namespace Fuel\Migrations;

class Delete_no_from_event_details
{
	public function up()
	{
		\DBUtil::drop_fields('event_details', array(
			'no'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('event_details', array(
			'no' => array('constraint' => 11,  'null' => false, 'type' => 'int'),
		));
	}
}
