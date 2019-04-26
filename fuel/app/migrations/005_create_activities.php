<?php

namespace Fuel\Migrations;

class Create_activities
{
	public function up()
	{
		\DBUtil::create_table('activities', array(
			'id'         => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'user_id'    => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'target'     => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'target_id'  => array('constraint' => 11, 'null' => true, 'type' => 'int'),
			'ip'         => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'host'       => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'user_agent' => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'created_at' => array( 'type' => 'timestamp', 'null' => true),
			'updated_at' => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at' => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('activities', 'admin_id', 'idx_activities_admin_id');
	}

	public function down()
	{
		\DBUtil::drop_table('activities');
	}
}
