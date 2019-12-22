<?php

namespace Fuel\Migrations;

class Rename_division_result_to_result_in_event_details
{
	public function up()
	{
		\DBUtil::modify_fields('event_details', array(
			'division_result' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'result'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('event_details', array(
			'result' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'division_result'),
		));
	}
}
