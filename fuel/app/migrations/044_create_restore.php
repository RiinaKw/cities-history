<?php

namespace Fuel\Migrations;

class Create_restore
{
	public function up()
	{
		\DBUtil::create_table('restore', array(
			'id'         => array(
				'type' => 'int',
				'unsigned' => true,
				'null' => false,
				'auto_increment' => true,
				'constraint' => 11
			),
			'sql'        => array('null' => false, 'type' => 'longtext'),
			'created_at' => array( 'type' => 'timestamp', 'null' => true),
			'updated_at' => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at' => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('restore');
	}
}
