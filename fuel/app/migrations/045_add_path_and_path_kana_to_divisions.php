<?php

namespace Fuel\Migrations;

class Add_path_and_path_kana_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'path'      => array('constraint' => 200,  'null' => false, 'type' => 'varchar', 'after' => 'fullname_kana'),
			'path_kana' => array('constraint' => 200,  'null' => false, 'type' => 'varchar', 'after' => 'path'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'path',
			'path_kana',
		));
	}
}
