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
			'id' => array('type' => 'int', 'null' => false, 'auto_increment' => true, 'unsigned' => true),
			'sql' => array('type' => 'longtext', 'null' => false),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),
		));
	}
}
