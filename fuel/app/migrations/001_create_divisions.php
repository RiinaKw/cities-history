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
			'postfix'            => array('constraint' => 20,  'null' => false, 'type' => 'varchar'),
			'postfix_kana'       => array('constraint' => 20,  'null' => false, 'type' => 'varchar'),
			'identify'           => array('constraint' => 50,  'null' => true, 'type' => 'varchar'),
			'parent_division_id' => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'start_event_id'     => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'end_event_id'       => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'created_at'         => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'         => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'         => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('divisions', 'parent_division_id', 'idx_divisions_parent_division_id');
	}

	public function down()
	{
		\DBUtil::drop_table('divisions');
	}
}
