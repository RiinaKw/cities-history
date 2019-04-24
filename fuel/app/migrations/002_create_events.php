<?php

namespace Fuel\Migrations;

class Create_events
{
	public function up()
	{
		\DBUtil::create_table('events', array(
			'id'                 => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'date'               => array( 'type' => 'date', 'null' => false),
			'type'               => array('constraint' => 100,  'null' => false, 'type' => 'varchar'),
			'created_at'         => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'         => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'         => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('events', 'date', 'idx_events_date');
	}

	public function down()
	{
		\DBUtil::drop_table('events');
	}
}
