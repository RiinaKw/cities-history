<?php

namespace Fuel\Migrations;

class Drop_restore
{
	public function up()
	{
		\DBUtil::drop_table('restore');
	}

	public function down()
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
}
