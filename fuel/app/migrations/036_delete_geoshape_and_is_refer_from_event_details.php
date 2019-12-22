<?php

namespace Fuel\Migrations;

class Delete_geoshape_and_is_refer_from_event_details
{
	public function up()
	{
		\DBUtil::drop_fields('event_details', array(
			'is_refer',
			'geoshape'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('event_details', array(
			'is_refer' => array('null' => false, 'type' => 'boolean', 'default' => false),
			'geoshape' => array('constraint' => 200,  'null' => true, 'type' => 'varchar'),
		));
	}
}
