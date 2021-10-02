<?php

namespace Fuel\Migrations;

class Create_event_details
{
	public function up()
	{
		\DBUtil::create_table('event_details', array(
			'id' => array(
				'type' => 'int',
				'unsigned' => true,
				'null' => false,
				'auto_increment' => true,
				'constraint' => 11
			),
			'event_id'           => array('constraint' => 11,  'null' => false, 'type' => 'int'),
			'division_id'        => array('constraint' => 11,  'null' => false, 'type' => 'int'),
			'division_result'    => array('constraint' => 20,  'null' => false, 'type' => 'varchar'),
			'created_at'         => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'         => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'         => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('event_details', 'event_id', 'idx_event_details_event_id');
		\DBUtil::create_index('event_details', 'division_id', 'idx_event_details_division_id');
	}

	public function down()
	{
		\DBUtil::drop_table('event_details');
	}
}
