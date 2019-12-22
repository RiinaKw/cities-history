<?php

namespace Fuel\Migrations;

class Rename_title_in_events
{
	public function up()
	{
		ini_set('memory_limit', '1G');

		\DBUtil::modify_fields('events', array(
			'type' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'title'),
		));
	}

	public function down()
	{
		ini_set('memory_limit', '1G');

		\DBUtil::modify_fields('events', array(
			'title' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'type'),
		));
	}
}
