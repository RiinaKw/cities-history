<?php

namespace Fuel\Migrations;

class Add_identifier_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'identifier' => array('constraint' => 50,  'null' => true, 'type' => 'varchar', 'after' => 'suffix_kana'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'identifier'
		));
	}
}
