<?php

namespace Fuel\Migrations;

class Rename_division_result_to_result_in_event_details
{
	public function up()
	{
		if ( \DBUtil::field_exists('event_details', array('division_result')))
		{
			\DBUtil::modify_fields('event_details', array(
				'division_result' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'result'),
			));
		}
	}

	public function down()
	{
		if ( \DBUtil::field_exists('event_details', array('result')))
		{
			\DBUtil::modify_fields('event_details', array(
				'result' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'division_result'),
			));
		}
	}
}
