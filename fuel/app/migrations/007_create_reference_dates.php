<?php

namespace Fuel\Migrations;

class Create_reference_dates
{
	public function up()
	{
		\DBUtil::create_table('reference_dates', array(
			'id'          => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'date'        => array( 'type' => 'date', 'null' => false),
			'description' => array('constraint' => 200, 'null' => false, 'type' => 'varchar'),
			'created_at'  => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'  => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'  => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('reference_dates', 'date', 'idx_reference_dates_date');
	}

	public function down()
	{
		\DBUtil::drop_table('reference_dates');
	}
}
