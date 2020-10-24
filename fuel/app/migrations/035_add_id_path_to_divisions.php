<?php

namespace Fuel\Migrations;

class Add_id_path_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'id_path' => array('constraint' => 500,  'null' => false, 'type' => 'varchar', 'after' => 'id'),
		));
		\DBUtil::create_index('divisions', 'id_path', 'idx_divisions_id_path');
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'id_path',
		));
	}
}
