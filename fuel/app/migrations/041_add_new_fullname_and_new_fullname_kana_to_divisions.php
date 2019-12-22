<?php

namespace Fuel\Migrations;

class Add_new_fullname_and_new_fullname_kana_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'new_fullname'      => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'after' => 'identifier'),
			'new_fullname_kana' => array('constraint' => 100,  'null' => false, 'type' => 'varchar', 'after' => 'new_fullname'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'new_fullname',
			'new_fullname_kana',
		));
	}
}
