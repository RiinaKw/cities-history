<?php

namespace Fuel\Migrations;

class Create_divisions
{
	public function up()
	{
		\DBUtil::create_table('divisions', array(
			'id'                 => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'name'               => array('constraint' => 20, 'null' => false, 'type' => 'varchar'),
			'name_kana'          => array('constraint' => 20, 'null' => false, 'type' => 'varchar'),
			'type'               => array('constraint' => 20,  'null' => false, 'type' => 'varchar'),
			'type_kana'          => array('constraint' => 20,  'null' => false, 'type' => 'varchar'),
			'parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'start_event'        => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'end_event'          => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'created_at'         => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'         => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'         => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('divisions', 'name', 'uq_divisions_name');
		\DBUtil::create_index('divisions', 'parent_division_id', 'uq_divisions_parent_division_id');
	}

	public function down()
	{
		\DBUtil::drop_table('divisions');
	}
}
