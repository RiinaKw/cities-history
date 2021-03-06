<?php

namespace Fuel\Migrations;

class Rename_type_to_title_in_events
{
	public function up()
	{
		if ( \DBUtil::field_exists('events', array('type')))
		{
			\DBUtil::modify_fields('events', array(
				'type' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'title'),
			));
		}
	}

	public function down()
	{
		if ( \DBUtil::field_exists('events', array('title')))
		{
			\DBUtil::modify_fields('events', array(
				'title' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'type'),
			));
		}
	}
}
