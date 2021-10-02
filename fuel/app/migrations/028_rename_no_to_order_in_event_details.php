<?php

namespace Fuel\Migrations;

class Rename_no_to_order_in_event_details
{
	public function up()
	{
		if (\DBUtil::field_exists('event_details', array('no'))) {
			\DBUtil::modify_fields('event_details', array(
				'no' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'order'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('event_details', array('order'))) {
			\DBUtil::modify_fields('event_details', array(
				'order' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'no'),
			));
		}
	}
}
