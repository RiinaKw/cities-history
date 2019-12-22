<?php

namespace Fuel\Migrations;

class Add_newcomment_to_events
{
	public function up()
	{
		\DBUtil::add_fields('events', array(
			'newcomment' => array('null' => true, 'type' => 'text', 'after' => 'title'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('events', array(
			'newcomment'
		));
	}
}
