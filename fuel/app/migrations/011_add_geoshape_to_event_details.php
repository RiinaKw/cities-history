<?php

namespace Fuel\Migrations;

class Add_geoshape_to_event_details
{
	public function up()
	{
		\DBUtil::add_fields('event_details', array(
			'geoshape'    => array('constraint' => 200,  'null' => true, 'type' => 'varchar'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('event_details', array(
			'geoshape'
		));
	}
}
