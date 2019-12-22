<?php

namespace Fuel\Migrations;

class Rename_new_fullname_and_new_fullname_kana_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'new_fullname'      => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'fullname'),
			'new_fullname_kana' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'fullname_kana'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('divisions', array(
			'fullname'      => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'new_fullname'),
			'fullname_kana' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'name' => 'new_fullname_kana'),
		));
	}
}
